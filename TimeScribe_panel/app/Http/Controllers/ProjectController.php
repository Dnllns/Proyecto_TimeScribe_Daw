<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    /**
     * -----------------------------------------------------------------
     * ---------------------ALTA BAJA MODIFICACION----------------------
     * -----------------------------------------------------------------
     */

    #region GESTION

    /**
     * VALIDAR
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'cient_id' => ['required', 'int'],
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:250'],
            'status' => ['required', 'tinyint'],
        ]);
    }

    /**
     * CREAR UN NUEVO PROYECTO
     * -----------------------
     * Recibe un Request con los datos del proyecto, y los inserta en las estructuras de BD
     * @param Request $data, los datos decibidos de la vista
     * @return View project/Pr_Select, la vista de seleccionar proyecto
     */
    protected function create(Request $data)
    {
        $user = auth()->user();

        if ($user->is_customer == User::CUSTOMER) {

            // SIN ACCESO
            // SOLO USUARIOS DESARROLLADORES
            return abort('403');

        } else {

            //Insert in projects table
            $insert = Project::create([
                // 'user_id' => $user->id,
                'client_id' => $data['client_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'created_by_id' => $user->id,
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

    }

    /**
     * ACTUALIZAR PROYECTO
     * ------------------------
     * Actualiza en BD las estructuras correspondientes a los datos
     * Recibe un Request con los datos obtenidos de la vista de editar proyecto
     * @param Request $data, los datos pasados por la vista
     * @param Integer $projectId, el id del proyecto
     * @return View seleccionar proyecto
     */
    protected function updateProject(Request $data, $projectId)
    {

        //Get the project
        $project = Project::find($projectId);

        //Udate fields
        $project->name = $data['name'];
        $project->description = $data['description'];
        // $project->client_id = $data['client_id'];
        //$project->status = $data['status'];

        //Update database
        $project->save();

        return $this->view_selectProject(); //Show the project selection view

    }

    /**
     * ELIMINAR PROYECTO
     * -----------------------
     * ELimina el proyecto con id pasado por parametro (lo deja INVISIBLE)
     * @param Integer $projectId, el id del proyecto
     * @return View seleccionar proyecto
     */
    public function deleteProject($projectId)
    {
        $project = Project::find($projectId);
        // $project->delete();
        $project->visible = Project::INVISIBLE;
        $project->save();
        return $this->view_selectProject();
    }

    #endregion GESTION

    /**
     * -----------------------------------------------------------------
     * ----------------------------VISTAS-------------------------------
     * -----------------------------------------------------------------
     */

    #region VISTAS

    /**
     * CREAR PROYECTO
     * ----------------
     * @return View
     */
    public function view_newProject()
    {
        return view('project/Pr_New');
    }

    /**
     * SELECCIONAR PROYECTO
     * ----------------------
     * @return View
     */
    public function view_selectProject()
    {
        $userProjects = auth()->user()->projects;
        return view('project/Pr_Select', ['userProjects' => $userProjects]);
    }

    /**
     * EDITAR PROYECTO
     * ----------------
     * @param Integer $projectId, el id del prollecto
     * @return View
     */
    public static function view_editProject($projectId)
    {
        $project = Project::find($projectId);
        $client = User::find($project->client_id);
        $taskGroups = $project->taskGroups;
        return view(
            'project/Pr_Edit',
            [
                'project' => $project,
                'client' => $client,
                'taskGroups' => $taskGroups,
            ]
        );

    }

    /**
     * DASHBOARD DEL PROYECTO
     * -------------------------
     * @param Integer $projectId, el id del proyecto a visualizar
     * @return View
     */
    public function view_dashboard($projectId)
    {

        $project = Project::find($projectId);
        $taskGroups = $project->taskGroups;

        return view(
            'project/Pr_Dashboard',
            [
                'taskGroups' => $taskGroups,
                'project' => $project,
            ]
        );
    }

    #endregion

    /**
     * -----------------------------------------------------------------
     * ----------------------------FUNCIONES----------------------------
     * -----------------------------------------------------------------
     */

    #region FUNCIONES

    /**
     * COMENZAR PROYECTO
     * -------------------
     * Actualiza las estructuras en BD correspondientes a el proyecto con id pasado por parametro -Si el proyecto no ha sido empezado-
     * le añade la fecha actual al campo start_date (tabla project)
     * Actualiza el stado del proyecto a haciendose (DOING)
     * @param Integer $projectId, el id del proyecto
     */
    public function startProject($projectId)
    {

        // Actualizar el proyecto en BD (tabla project)
        // Solo si no se ha empezado ya

        $project = Project::find($projectId);

        if ($project->start_date != null && $project->status == Project::STATUS_TODO) {

            // Añadir la fecha de inicio al proyecto
            // Actualizar el estado del proyecto a haciendose

            $project->start_date = Carbon::now()->toDateTimeString();
            $project->status = Project::STATUS_DOING;
        }
    }


    

    #endregion
    

}

