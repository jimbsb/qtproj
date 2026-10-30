export function validateForm(data) {
    const _data = JSON.parse(JSON.stringify(data));
    console.log(_data);
    
    return _data;
}