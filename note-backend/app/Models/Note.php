<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['title', 'body', 'category', 'type', 'isdone'];
    
    // Define the possible types
    const TYPE_TASK = 'task';
    const TYPE_NOTE = 'note';
    
    // Validation rules for type
    public static function getTypeValidationRule()
    {
        return 'required|in:' . self::TYPE_TASK . ',' . self::TYPE_NOTE;
    }
    
    // Scope methods for filtering by type
    public function scopeTasks($query)
    {
        return $query->where('type', self::TYPE_TASK);
    }
    
    public function scopeNotes($query)
    {
        return $query->where('type', self::TYPE_NOTE);
    }
    
    // Helper method to check if item is a task
    public function isTask()
    {
        return $this->type === self::TYPE_TASK;
    }
    
    // Helper method to check if item is a note
    public function isNote()
    {
        return $this->type === self::TYPE_NOTE;
    }
    
    // Scope for completed items
    public function scopeDone($query)
    {
        return $query->where('isdone', true);
    }
    
    // Scope for incomplete items
    public function scopeNotDone($query)
    {
        return $query->where('isdone', false);
    }
}