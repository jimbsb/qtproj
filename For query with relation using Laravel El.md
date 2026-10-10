For query with relation using Laravel Eloquent

Yes — this is exactly what Eloquent relationships do. You define a `belongsTo` relationship on the model, then use `with()` to load it. The foreign key column stays as-is, and the related record gets nested as a sub-array.

### 🎯 The Setup

**Migration — `posts` table:**
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();   // the foreign key
    $table->string('title');
    $table->timestamps();
});
```

**Model — `app/Models/Post.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);   // uses user_id by convention
    }
}
```

**Model — `app/Models/User.php`:**
```php
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### ✅ Getting the Nested Shape

```php
$posts = Post::with('user')->get();

return $posts->toArray();
```

**Output:**
```php
[
    [
        'id'         => 1,
        'user_id'    => 5,               // ← FK stays
        'title'      => 'Hello',
        'user'       => [                // ← related record nested
            'id'    => 5,
            'name'  => 'Alice Smith',
        ],
        'created_at' => '...',
        'updated_at' => '...',
    ],
    // ...
]
```

That's exactly the shape you described.

### 🎯 Controlling the Nested Fields

If you don't want all user columns, use `with()` with a column filter:

```php
$posts = Post::with('user:id,name')->get();
```

**Important:** When you filter columns, you **must** include the foreign key (`id` here, since `user.id` is what links them). Otherwise, Eloquent can't match them up and the relation returns `null`.

```php
Post::with('user:id,name')->get();
// user: [id => 5, name => 'Alice']
```

### 🎯 Custom Foreign Key

If your column isn't named `user_id`, pass it explicitly:

```php
public function user()
{
    return $this->belongsTo(User::class, 'assigned_to_id', 'id');
    //                                     foreign key,   owner key
}
```

Example:
```php
// posts table has: id, author_id, title
public function author()
{
    return $this->belongsTo(User::class, 'author_id');
}
```

Then:
```php
Post::with('author:id,name')->get();
// ['id' => 1, 'author_id' => 5, 'author' => ['id' => 5, 'name' => 'Alice']]
```

### 🎯 In Your Livewire Ticket Table

Since your tickets have `user_id`, `assigned_to`, `responder_id`, etc., you'd set up:

**`app/Models/Ticket.php`:**
```php
class Ticket extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responder()
    {
        return $this->belongsTo(Responder::class);
    }
}
```

**Fetch with relationships:**
```php
$tickets = Ticket::with([
    'user:id,name',
    'assignee:id,name',
    'responder:id,name',
])->get();
```

**Each row comes back as:**
```php
[
    'id'          => 1,
    'user_id'     => 5,
    'assigned_to' => 7,
    'responder_id'=> 2,
    'subject'     => '...',
    'user'        => ['id' => 5, 'name' => 'Sarah Jenkins'],
    'assignee'    => ['id' => 7, 'name' => 'Alice Smith'],
    'responder'   => ['id' => 2, 'name' => 'IT'],
]
```

### ⚠️ Since Your Testing Schema Uses Strings

Right now your testing migration stores `user_id` as a **string** (a name, not an integer). That won't work with `belongsTo` because Eloquent needs to match numeric IDs.

**Two options:**

**Option A — Keep strings, skip Eloquent relationships.** Join manually or just store the display name as-is in `assigned_to`. That's what you have now, and it works fine for a prototype.

**Option B — Move to real foreign keys.** Once you swap the migration to use `foreignId('user_id')->constrained()`, the relationships above work immediately.

### 🎯 Avoid N+1 Queries

The biggest performance trap in Laravel is lazy-loading a relationship inside a loop:

```php
// ❌ N+1: 1 query for posts + 100 queries for users
foreach (Post::all() as $post) {
    echo $post->user->name;
}

// ✅ 2 queries total
foreach (Post::with('user')->get() as $post) {
    echo $post->user->name;
}
```

**Rule:** Always `with()` the relationship when you know you'll need it in the view.

### 🎯 Nested Relationships

You can chain relationships too:

```php
Post::with('user.profile')->get();
// → post → user → profile
```

Or load multiple at once:
```php
Ticket::with(['user:id,name', 'assignee:id,name', 'responder:id,name'])->get();
```

### 🎯 Lazy Loading After the Fact

If you fetched records without `with()`, you can load relations later:

```php
$tickets = Ticket::all();
$tickets->load('user', 'assignee');
```

Or on a single model:
```php
$ticket->load('user');
```

### 💡 Summary Table

| Goal | Code |
| :--- | :--- |
| Define relationship | `return $this->belongsTo(User::class);` |
| Custom FK column | `return $this->belongsTo(User::class, 'author_id');` |
| Eager load | `Post::with('user')->get();` |
| Eager load with select | `Post::with('user:id,name')->get();` |
| Multiple relations | `Post::with(['user', 'comments'])->get();` |
| Load after fetch | `$posts->load('user');` |

### 🎯 What `toArray()` Actually Produces

```php
Post::with('user')->first()->toArray();
```

```php
[
    'id'         => 1,
    'user_id'    => 5,
    'title'      => 'Hello',
    'created_at' => '2026-10-03T12:00:00.000000Z',
    'updated_at' => '2026-10-03T12:00:00.000000Z',
    'user'       => [
        'id'   => 5,
        'name' => 'Alice Smith',
    ],
]
```

The FK stays in the parent, and the related model appears as a nested key named after the relationship method. That's exactly the format you asked for.

**Bottom line:** Yes, `belongsTo` + `with()` gives you exactly `[id => ..., user => [user_id => ..., name => ...]]`. The only requirement is that the foreign key column contains IDs that match the parent table's key — so you'll need to move away from your testing strings when you're ready for real relationships.