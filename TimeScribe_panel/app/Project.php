<?php

namespace App;

use App\WorkGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Project extends Model
{

    // -------------------------------MODELDATA--------------------------------------
    // ------------------------------------------------------------------------------

    #region data

    //PROJECT STATUSS
    const STATUS_TODO = 0;
    const STATUS_DOING = 1;
    const STATUS_DONE = 2;

    //PROJECT PERMISSIONS
    const PERM_ALL = 1;
    const PERM_WORK = 0;
    const PERM_CLIENT = 2;

    //VISIBILITY
    const INVISIBLE = 0;
    const VISIBLE = 1;

    //DB TABLE
    protected $table = 'projects';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'status',
        'visible',
        'client_id',
        'workgroup_id',
        'start_date',
        'finish_date',
    ];

    #endregion

    // -------------------------------RELATIONS--------------------------------------
    // ------------------------------------------------------------------------------

    #region relations

    // 1:N
    public function workgroups()
    {
        return $this->belongsTo('App\WorkGroup', 'workgroup_id', 'id');
        // return $this->belongsTo('App\WorkGroup');

    }

    //N:N USERS
    public function users()
    {
        return $this->belongsToMany('App\User', 'users_projects')->withPivot('permissions');
        // return $this->belongsToMany('App\User', 'users_projects', 'user_id', 'project_id')
        // ->withPivot('permissions');

    }

    //1:N TASKGROUP
    public function taskGroups()
    {
        return $this->hasMany('App\TaskGroup');
    }

    //1:1 BILLS
    public function bill()
    {
        return $this->hasOne('App\Bill');
    }

    public function clientInvitation()
    {
        // return $this->belongsToMany('App\WorkGroupInvitation', 'workgroups_workgroupinvitations', 'workgroup_id', 'invitation_id');
        return $this->hasMany('App\ClientInvitation', 'project_id', 'id');
    }

    #endregion

    // -------------------------------FUNCTIONS--------------------------------------
    // ------------------------------------------------------------------------------

    #region functions

    /**
     * GET PORCENTAJE
     * ----------------
     * Obtiene el porcentaje de completado para este proyecto basandose
     * en el porcentaje de completado que tienen sus grupos de tarea.
     * @return Integer, el porcentaje calculado
     */
    public function getPercentCompleted()
    {

        $taskGroups = $this->taskGroups;
        $percent = 0;
        $result = 0;
        $count = 0;

        foreach ($taskGroups as $taskgroup) {

            if (count($taskgroup->tasks) != 0) {
                $percent += $taskgroup->getPercentCompleted();
                $count += 1;
            }
        }

        try {
            $result = $percent / $count;
        } catch (\Exception $e) {
            //posible division por 0
        }

        return $result;
    }


    /**
     * GET DESARROLLADORES
     * ----------------------
     * Obtiene los desarrolladores que pueden participar en el proyecto
     * @return Collection[User], una coleccion con los usuarios
     */
    public function getDevelopers()
    {
        return $this->users()->where('permissions', Project::PERM_WORK)->orWhere('permissions', Project::PERM_ALL)->get();
    }



    /**
     * Obtiene los permisos que tiene el usuario en este proyecto
     */
    public function getUserPermission()
    {

        $permissions = $this->users()->where('user_id', Auth::user()->id)
        ->first()->pivot->permissions;

        return $permissions;


    }

    public function getPermission($userId)
    {

        $permissions = $this->users()->where('user_id', $userId)
        ->first()->pivot->permissions;

        return $permissions;


    }


    public function getClient()
    {

        $clientId = $this->client_id;


        if ($clientId == null) {
            return null;
        } else {

            return User::find($clientId);

        }
    }

    public function getClientinvitation(){


        $invitation = $this->clientInvitation()->first();
        return $invitation;


    }

    public function getBreadCrumbs()
    {


        $wgroup_name = $this->workgroups()->first()->name;
        $wgroup_id = $this->workgroups()->first()->id;

        $wg_route = "<a class='text-info' href='/workgroup-show/" . $wgroup_id . "'>" . $wgroup_name  . "</a>";

        $project_route = "<a class='text-info' href='/project-show/" . $this->id . "'>" . $this->name  . "</a>";


        return $wg_route . "<span class='text-secondary'>/</span>" . $project_route;
    }

    #endregion

}
