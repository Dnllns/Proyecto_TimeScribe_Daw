<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkGroup extends Model
{



    /**
     * -----------------------------CLASS----------------------------
     * ---------------------------------------------------------------
     */

    #region class

    protected $table = 'workgroups';
    protected $fillable = ['name'];
    public $timestamps = false;

    #endregion



    /**
     * --------------------------RELATIONS----------------------------
     * ---------------------------------------------------------------
     */


    #region Relations

    //1:N
    public function users()
    {
        return $this->hasMany('App\User', 'workgroup_id', 'id');
    }

    //1:N
    public function projects()
    {
        return $this->hasMany('App\Project', 'workgroup_id', 'id');
    }

    //N:N
    public function workGroupInvitations()
    {
        // return $this->belongsToMany('App\WorkGroupInvitation', 'workgroups_workgroupinvitations', 'workgroup_id', 'invitation_id');
        return $this->hasMany('App\WorkGroupInvitation', 'workgroup_id', 'id');
    }

    #endregion


    /**
     * --------------------------FUNCIONES----------------------------
     * ---------------------------------------------------------------
     */

    #region Functions

    /**
     * Obtener los desarrolladores que pertenecen al Workgroup
     * @return Collection[User]
     */
    public function getAllDevelopers()
    {
        return $this->users()->get();
    }

    public function getAllClients()
    {

        return $this->users()->where('is_client', '1')->get();
    }

    public function getBreadCrumbs()
    {
        return "<a class='text-info' href='" . route('v-wg-show', $this->id) . "'>" . $this->name . "</a>";
    }


    #endregion



}
