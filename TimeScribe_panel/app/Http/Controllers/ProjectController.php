<?php

namespace App\Http\Controllers;
use App\Project;
use App\User;
use App\WorkGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\WorkGroupController;
use Illuminate\Support\Facades\Auth;



class ProjectController extends Controller
{

    /**
     * ----------------------------GESTION------------------------------
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
        protected function insertProject(Request $data, $workGroupId)
        {
            $user = auth()->user();

            //Insert in projects table
            $newProject = Project::create([
                // 'user_id' => $user->id,
                'client_id' => $data['client_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'workgroup_id' => $workGroupId,
            ]);



            //Attach to project_user table
            $user->projects()->attach(
                $newProject->id,
                [
                    'project_id' => $newProject->id,
                    'user_id' => $user->id,
                    'permissions' => Project::PERM_ALL, //ADMIN
                ]
            );

            //Devolver la vista de seleccionar proyecto
            return WorkGroupController::view_show($workGroupId);


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

            // return $this->view_selectProject(); //Show the project selection view

            return $this->view_showProject($project->id);


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
     * ----------------------------VISTAS-------------------------------
     * -----------------------------------------------------------------
     */

    #region VISTAS

        /**
         * CREAR PROYECTO
         * ----------------
         * @return View
         */
        public function view_newProject($workGroupId)
        {
            $workGroup = Workgroup::find($workGroupId);
            return view('project/new', [ 'workGroup' => $workGroup]);
        }

        /**
         * SELECCIONAR PROYECTO
         * ----------------------
         * @return View
         */
        public function view_selectProject()
        {

            //Obtener los proyectos visibles del usuario
            $userProjects = auth()->user()->projects;
            if ($userProjects->count() == 0) {
                $userProjects = null;
            }



            return view('project/select', ['userProjects' => $userProjects]);
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
            $client = null;

            if($project->client_id != null){
                $client = User::find($project->client_id);
            }

            $taskGroups = $project->taskGroups()->get();      //->where("visible", 0)
            $devList = $project->users()
            ->where('is_client', null)->orWhere('is_client', '0')
            ->get();


            // Obtener los desarrolladores del grupo que no se han asignado al proyecto
            $workGroupDevs = WorkGroup::find($project->workgroup_id)->users()
            ->where('is_client', null)->orWhere('is_client', '0')
            ->get();
            $notAsignedDevs = $workGroupDevs->diff($devList);

            //SET TO NULL

            if($notAsignedDevs->count() == 0){
                $notAsignedDevs = null;
            }

            if($devList->count() == 0){
                $devList = null;
            }

            if($taskGroups->count() == 0){
                $taskGroups = null;
            }


            return view(
                'project/mod/mod',
                [
                    'project' => $project,
                    'client' => $client,
                    'taskGroups' => $taskGroups,
                    'devList' => $devList,
                    'workGroupDevs' => $notAsignedDevs
                ]
            );

        }

        /**
         * DASHBOARD DEL PROYECTO
         * -------------------------
         * @param Integer $projectId, el id del proyecto a visualizar
         * @return View
         */
        public static function view_showProject($projectId)
        {

            $project = Project::find($projectId);
            $taskGroups = $project->taskGroups;

            if($taskGroups->count() ==  0){
                $taskGroups = null;
            }

            $permissions = $project->getUserPermission(Auth::user()->id);


            return view(
                'project/show/show',
                [
                    'taskGroups' => $taskGroups,
                    'project' => $project,
                    'permissions' => $permissions

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
        public static function startProject($projectId)
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


        /**
         * Añade un developer a un proyecto con un permiso
         */
        public function addDeveloper($projectId, $developerId, $permissionType){

            $project = Project::find($projectId);

            $project->users()->attach(
                $developerId,
                [
                    'project_id' => $projectId,
                    'user_id' => $developerId,
                    'permissions' => $permissionType,
                ]
            );

        }


        /**
         * Elimina a un developer de un proyecto
         */
        public function delDeveloper($projectId, $developerId){

            $project = Project::find($projectId);
            $project->users()->detach($developerId);

        }



        public function removeClient($projectId, $clientId){

            $project = Project::find($projectId);
            $project->client_id = null;
            $project->users()->detach($clientId);
            $project->save();

            $user = User::find($clientId);
            $user->delete();


        }


    #endregion

}
