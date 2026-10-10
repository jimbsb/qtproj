<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $responders = ['Admin', 'ITSO', 'CAMO', 'DSO'];
        $priorities = ['High', 'Medium', 'Low'];
        $statuses   = ['Open', 'In Progress', 'Resolved', 'Closed'];
        $categories = ['Software', 'Hardware','Networking','Internet', 'Electrical', 'HVAC', 'Furniture', 'Plumbing'];
        $assignees  = [
            'Alice Smith',
            'David Kim',
            'Engineering Team',
            'Facility Team',
            null,
            null,   // extra nulls = more "Unassigned" rows
        ];

        $status = $this->faker->randomElement($statuses);

        // Only set lifecycle timestamps that make sense for the status
        $resolvedAt = in_array($status, ['Resolved', 'Closed'])
            ? $this->faker->dateTimeBetween('-14 days', 'now')
            : null;

        $closedAt = $status === 'Closed'
            ? $this->faker->dateTimeBetween('-7 days', 'now')
            : null;

        $createdAt = $this->faker->dateTimeBetween('-60 days', 'now');

        return [
            'ticket_id'           => 'TK-' . str_pad((string) $this->faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
            'user_id'             => $this->faker->name(),
            'responder_id'        => $this->faker->randomElement($responders),
            'subject'             => $this->faker->sentence(6),
            'description'         => $this->faker->paragraph(3),
            'priority_id'         => $this->faker->randomElement($priorities),
            'status_id'           => $status,
            'service_category_id' => $this->faker->randomElement($categories),
            'service_start_at'    => $this->faker->optional(0.6)->dateTimeBetween('-60 days', 'now')?->format('Y-m-d H:i:s'),
            'service_end_at'      => $this->faker->optional(0.4)->dateTimeBetween('-60 days', 'now')?->format('Y-m-d H:i:s'),
            'assigned_to'         => $this->faker->randomElement($assignees),
            'resolved_at'         => $resolvedAt?->format('Y-m-d H:i:s'),
            'closed_at'           => $closedAt?->format('Y-m-d H:i:s'),
            'created_at'          => $createdAt,
            'updated_at'          => $createdAt,
        ];
    }

    /**
     * State: only open tickets.
     */
    public function open(): static
    {
        return $this->state(fn () => [
            'status_id'   => 'Open',
            'resolved_at' => null,
            'closed_at'   => null,
        ]);
    }

    /**
     * State: only resolved or closed tickets.
     */
    public function resolved(): static
    {
        return $this->state(function () {
            $status = $this->faker->randomElement(['Resolved', 'Closed']);

            return [
                'status_id'   => $status,
                'resolved_at' => $this->faker->dateTimeBetween('-14 days', 'now')->format('Y-m-d H:i:s'),
                'closed_at'   => $status === 'Closed'
                    ? $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d H:i:s')
                    : null,
            ];
        });
    }

    /**
     * State: high priority only.
     */
    public function highPriority(): static
    {
        return $this->state(fn () => [
            'priority_id' => 'High',
        ]);
    }

    /**
     * State: unassigned.
     */
    public function unassigned(): static
    {
        return $this->state(fn () => [
            'assigned_to' => null,
        ]);
    }
}