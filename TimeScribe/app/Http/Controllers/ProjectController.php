<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'cient_id' => ['required', 'int'],
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:250'],
            'status' => ['required', 'tinyint'],
        ]);
    }

    protected function create(Request $data)
    {
        $user = auth()->user();

        //Insert in projects table
        $insert = Project::create([
            'user_id' => $user->id,
            'client_id' => $data['client_id'],
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        //Attach to project_user table
        $user->projects()->attach(
            $insert->id,
            [
                'project_id' => $insert->id,
                'user_id' => $user->id,
                'permissions' => Project::PERM_ALL,
            ]
        );

        return $this->view_selectProject(); //Show the project selection view
    }

    protected function updateProject(Request $data, $id)
    {

        //Get the project
        $project = Project::find($id);


        // dd($project);exit;

        //Udate fields
        $project->name = $data['name'];
        $project->description = $data['description'];
        // $project->client_id = $data['client_id'];
        //$project->status = $data['status'];

        //Update database
        $project->save();

        return $this->view_selectProject(); //Show the project selection view


    }

    //VISTAS

    public function view_newProject()
    {
        return view('project/new');
    }

    public function view_selectProject()
    {
        $userProjects = auth()->user()->projects;
        return view('project/select', ['userProjects' => $userProjects]);
    }

    public function view_editProject($projectId)
    {
        $project = Project::find($projectId);
        $client = User::find($project->client_id);
        return view(
            'project/edit',
            [
                'project' => $project,
                'client' => $client,
            ]
        );

    }

}
