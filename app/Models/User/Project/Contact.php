<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    public $table = "user_project_contacts";
    protected $guarded = [];

    public function createContact($userId, $agentId, $request)
    {
        self::create([
            'user_id' => $userId,
            'agent_id' => $agentId,
            'project_id' => $request['project_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'message' => $request['message'],
        ]);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function projectContent($langId = null)
    {
        return $this->belongsTo(ProjectContent::class, 'project_id', 'project_id')
            ->when($langId, function ($query) use ($langId) {
                $query->where('language_id', $langId);
            });
    }
}
