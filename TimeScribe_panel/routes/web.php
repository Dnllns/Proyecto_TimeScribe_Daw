<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('layouts.home');
});

//LOGIN - REGISTER
Auth::routes();


Route::get('/logout', function(){
    Session::flush();
    Auth::logout();
    return Redirect::to("/login")
      ->with('message', array('type' => 'success', 'text' => 'You have successfully logged out'));
})->name('f-logout');

//HOME
Route::get('/home', 'HomeController@index')->name('home');


//-----------------------------WORKGROUP--------------------------
//----------------------------------------------------------------

#region workgroup

    #region VISTAS

        // ALTA
        Route::post('/workgroup', 'WorkGroupController@view_S')
        ->name('v-wg-v')->middleware('auth');

        // ALTA
        Route::get('/workgroup-new', 'WorkGroupController@view_newWorkGroup')
        ->name('v-wg-new')->middleware('auth');

        // MOD
        Route::get('/workgroup-mod/{workGroupId}/{isNew?}', 'WorkGroupController@view_modWorkGroup')
        ->name('v-wg-mod')->middleware('auth');

        // SELECCIONAR PROYECTO
        Route::get('/workgroup-show/{workGroupId}', 'WorkGroupController@view_show')
        ->name('v-wg-show')->middleware('auth');

    #endregion

    #region BD

        //Insertar workgroup
        Route::post('/workgroup-new-bd', 'WorkGroupController@insertWorkgroup')
        ->name('f-wg-new')->middleware('auth');

        //Editar workgroup
        Route::post('/workgroup-mod-bd/{workGroupId}', 'WorkGroupController@edit')
        ->name('f-wg-mod')->middleware('auth');

        //Insertar invitacion al workgroup y enviar email
        // Route::post('/workgroup-invite-bd/{workGroupId}/{developerEmail}', 'WorkGroupController@createInvitation')
        // ->name('f-wg-invite')->middleware('auth');

    #endregion


    #region email

        //(Invitacion de email) Unirse a un grupo
        Route::get('/email/accept-invitation/{invitationId}/{hash}', 'WorkGroupInvitationController@acceptInvitation')
        ->name('f-wg-acceptinvitation');

        //Enviar invitacion
        Route::post('/email/send-workgroup-invitation', 'WorkGroupInvitationController@inviteUser')
        ->name('f-wg-invite')->middleware('auth');

        //Eliminar invitacion a grupo
        Route::get('/email/remove-wg-invitation/{invitationId}', 'WorkGroupInvitationController@removeInvitation')
        ->name('f-wg-removeinvitation')->middleware('auth');


    #endregion

    #region newUser

        //Enviar invitacion
        Route::post('/register/join-workgroup/{workgroupId}', 'WorkGroupInvitationController@registerUser')
        ->name('f-wg-registeruser')->middleware('auth');

    #endregion

#endregion

//-----------------------------PROYECTO---------------------------
//----------------------------------------------------------------

#region Proyecto

    #region vistas

        // ALTA
        // Vista de crear un proyecto
        Route::get('/project-new/{workGroupId}', 'ProjectController@view_newProject')
        ->name('v-pj-new')->middleware('auth');

        // MOD
        // Vista de editar datos de un proyecto
        Route::get('/project-mod/{projectId}', 'ProjectController@view_editProject')
        ->name('v-pj-mod')->middleware('auth');

        // VISUALIZAR PROYECTO
        Route::get('/project-show/{projectId}', 'ProjectController@view_showProject')
        ->name('v-pj-show')->middleware('auth');

        //  // BAJA
        //  Route::post('/taskgroup-show/{projectId}', 'ProjectController@v_showBeforeDeleteTaskgroup')
        //  ->name('v-pj-show_before_delete_tg')->middleware('auth');

    #endregion

    #region BD

        // ALTA
        Route::post('/project-new-bd/{workGroupId}', 'ProjectController@insertProject')
        ->name('f-pj-new')->middleware('auth');

        // BAJA
        Route::get('/project-del-bd/{projectId}', 'ProjectController@deleteProject')
        ->name('f-pj-del')->middleware('auth');

        // MOD
        Route::post('/project-mod-bd/{projectId}', 'ProjectController@updateProject')
        ->name('f-pj-mod')->middleware('auth');

        // ADD DEVELOPER
        Route::get('/project-add-developer-bd/{projectId}/{developerId}/{permissionType}', 'ProjectController@addDeveloper')
        ->name('f-pj-adddev')->middleware('auth');

        // DEL DEVELOPER
        Route::get('/project-del-developer-bd/{projectId}/{developerId}', 'ProjectController@delDeveloper')
        ->name('f-pj-deldev')->middleware('auth');


    #endregion

#endregion

//----------------------------TASKGROUP---------------------------
//----------------------------------------------------------------

#region TaskGroup

    #region Vistas

        // ALTA
        Route::get('/taskgroup-new/{projectId}', 'TaskGroupController@view_newTaskGroup')
        ->name('v-tg-new');

        // MOD
        Route::get('/taskgroup-mod/{taskGroupId}', 'TaskGroupController@view_editTaskGroup')
        ->name('v-tg-mod')->middleware('auth');



    #endregion

    #region BD

        // ALTA
        Route::post('/taskgroup-new-bd/{projectId}', 'TaskGroupController@create')
        ->name('f-tg-new')->middleware('auth');

        // BAJA
        Route::get('/taskgroup-del-bd/{taskGroupId}', 'TaskGroupController@deleteTaskGroup')
        ->name('f-tg-del')->middleware('auth');

        // MOD
        Route::post('/taskgroup-mod-bd/{taskGroupId}', 'TaskGroupController@updateTaskGroup')
        ->name('f-tg-mod')->middleware('auth');

    #endregion


#endregion

//-------------------------------TASK-----------------------------
//----------------------------------------------------------------

#region Task

    #region Vistas

        // Alta
        Route::get('/task-new/{taskGroupId}', 'TaskController@view_newTask')
        ->name('v-ts-new');

        // Mod
        Route::get('/task-mod/{taskId}', 'TaskController@view_editTask')
        ->name('v-ts-mod');

    #endregion

    #region BD

        // Alta Baja y Mod
        #region gestion

            // ALTA
            Route::post('/task-new-bd/{taskGroupId}', 'TaskController@create')
            ->name('f-ts-new')->middleware('auth');

            // BAJA
            Route::get('/task-del-bd/{taskId}', 'TaskController@deleteTask')
            ->name('f-ts-del')->middleware('auth');

            // MOD
            Route::post('/task-mod-bd/{taskId}', 'TaskController@updateTask')
            ->name('f-ts-mod')->middleware('auth');

        #endregion

        #region estado de la tarea

            // INICIADO
            Route::get('/task-setstarted-bd/{taskId}', 'TaskController@setStarted')
            ->name('f-ts-setstarted')->middleware('auth');

            // TERMINADO
            Route::get('/task-setdone-bd/{taskId}', 'TaskController@setDone')
            ->name('f-ts-setdone')->middleware('auth');

            // ELIMINADO
            Route::get('/task-setdeleted-bd/{taskId}', 'TaskController@setDelete')
            ->name('f-ts-setdeleted')->middleware('auth');

            //Obtener tiempo trabajado
            Route::get('/ct-getworkedtime/{taskId}', 'TaskController@getWorkedTime')
            ->name('ct_gwt')->middleware('auth');

        #endregion


        #region Task Mod

            // Añadir desarrollador a la tarea
            Route::get('/task-adddev-bd/{taskId}/{devId}', 'TaskController@addDeveloper')
            ->name('f-ts-adddev')->middleware('auth');

            // Eliminar desarrollador de la tarea
            Route::get('/task-deldev-bd/{taskId}/{devId}', 'TaskController@delDeveloper')
            ->name('f-ts-deldev')->middleware('auth');

        #region


        #region STICKY CHRONO

            // Iniciar cronómetro
            Route::get('/chrono-start-bd/{taskId}', 'TaskController@startCount')
            ->name('f-ts-ch-start')->middleware('auth');

            // Parar cronometro
            Route::get('/chrono-stop-bd', 'TaskController@stopCount')
            ->name('f-ts-ch-stop')->middleware('auth');

            //Eliminar timerecords borrador
            Route::get('/chrono-reset-bd/{taskId}', 'TimeRecordController@removeLastTimerecordsInteraction')
            ->name('f-ts-ch-reset')->middleware('auth');

            //Actualizar timerecords borrador a finalizados
            Route::get('/chrono-finish-bd/{taskId}', 'TimeRecordController@setFinalTimerecordsInteraction')
            ->name('f-ts-ch-finish')->middleware('auth');

        #endregion

    #endregion

#endregion

//-----------------------------CLIENT-----------------------------
//----------------------------------------------------------------

#region Client

    Route::get('/client-dashboard/{clientId}', 'ClientController@view_showClientProjects')
    ->name('client_dashboard')->middleware('auth');

    Route::post('/email/send-client-invitation', 'ClientInvitationController@inviteClient')
    ->name('invite_client')->middleware('auth');

    Route::get('/email/client-accept-invitation/{invitationId}/{hash}', 'ClientInvitationController@acceptInvitation')
        ->name('f-cl-acceptinvitation');

    Route::get('/email/client-delete-invitation/{invitationId}', 'ClientInvitationController@deleteInvitation')
    ->name('f-cl-deleteinvitation');

    Route::post('/register-client/{projectId}/{invitationId}', 'ClientInvitationController@registerClient')
    ->name('f-pj-register-client')->middleware('guest');


    Route::get('/remove-client/{projectId}/{clientId}', 'ProjectController@removeClient')
    ->name('f-wg-removeclient')->middleware('auth');




#endregion




//------------------------------USER------------------------------
//----------------------------------------------------------------

#region User

Route::get('/remove-developer/{userId}', 'UserController@deleteUser')
->name('f-wg-removedev')->middleware('auth');


Route::post('/register-developer/{workgroupId}/{invitationId}', 'WorkGroupInvitationController@registerUser')
->name('f-wg-register-developer')->middleware('guest');


#endregion




