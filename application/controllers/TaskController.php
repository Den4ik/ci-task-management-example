<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once 'BaseController.php';

class TaskController extends BaseController
{
    /**
     * @var Task
     */
    public $taskModel;

    /**
     * TaskController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('task', 'taskModel');
    }

    /**
     * Renderer sidebar
     */
    public function loadSidebar()
    {
        return $this->load->view('page/sidebar');
    }

    /********************** Actions **********************/

    /**
     * Index action
     *
     * @throws Exception
     */
    public function index()
    {
        $this->initialize();
        $this->_redirect('taskList');
    }


    /**
     * Task list action
     *
     * @param null $page
     */
    public function taskList($page = null)
    {
        $this->initialize();
        $this->load->library('pagination');

        try {
            $taskCollectionForPagination = $this->taskModel->getCollection();
            $this->prepareGridCollection(
                $taskCollectionForPagination,
                ['title', 'description']
            );
            $this->initializePagination($this->getUrl('taskList'), $taskCollectionForPagination->count());

            $taskCollection = $this->taskModel->getCollection();
            $taskCollection->limit($this->gridPageLimit, $page ? $this->gridPageLimit * ($page - 1) : 0);
            $this->prepareGridCollection(
                $taskCollection,
                ['title', 'description'],
                'task.grid'
            );
        } catch (Exception $e) {
            log_message('exception', $e->getMessage());
            $this->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        }

        $gridLinks = [
            'change_status' => [
                'link' => $this->getUrl('taskChangeStatus/{task_id}'),
                'icon-class' => 'fas fa-check',
                'btn-class' => 'btn-success',
                'btn-title' => lang('btn_switch_status')
            ],
            'edit' => [
                'link' => $this->getUrl('taskEdit/{task_id}'),
                'icon-class' => 'fas fa-pencil-alt',
                'btn-class' => 'btn-info',
                'btn-title' => lang('btn_edit')
            ],
            'remove' => [
                'link' => $this->getUrl('taskRemove/{task_id}'),
                'icon-class' => 'fas fa-trash-alt',
                'btn-class' => 'btn-danger',
                'btn-title' => lang('btn_remove')
            ]
        ];


        $this->addContentBlock('task.grid', 'common/grid');
        $this->setContentData('task.grid', 'title', lang('task_management'))
            ->setContentData('task.grid', 'header_icon_class', 'fas fa-tasks')
            ->setContentData('task.grid', 'grid_title', lang('task_list'))
            ->setContentData('task.grid', 'allow_add', true)
            ->setContentData('task.grid', 'add_new_url', $this->getUrl('taskEdit'))
            ->setContentData('task.grid', 'grid_search_url', $this->getUrl('taskList'))
            ->setContentData('task.grid', 'columns', [
                'title' => [
                    'title' => lang('title'),
                    'index' => 'title',
                    'class' => 'col-sm-2'
                ],
                'description' => [
                    'title' => lang('description'),
                    'getter' => 'getShortDescription',
                    'class' => 'col-sm-3'
                ],
                'is_complete' => [
                    'title' => lang('status'),
                    'getter' => 'getStatus',
                    'class' => 'col-sm-2'
                ],
                'created_at' => [
                    'title' => lang('created_at'),
                    'index' => 'created_at',
                    'class' => 'col-sm-2'
                ],
                'updated_at' => [
                    'title' => lang('updated_at'),
                    'index' => 'updated_at',
                    'class' => 'col-sm-2'
                ],
                'actions' => [
                    'title' => lang('actions'),
                    'class' => 'col-sm-2 text-center',
                    'links' => $gridLinks
                ]
            ])
            ->setContentData('task.grid', 'collection', $taskCollection);

        $this->renderLayout();
    }

    /**
     * Remove task action
     *
     * @param null $taskId
     */
    public function taskRemove($taskId = null)
    {
        $this->initialize();
        if ($taskId) {
            $task = $this->taskModel->load($taskId);
            if ($task->getId()) {
                try {
                    $task->delete();
                } catch (Exception $e) {
                    log_message('exception', $e->getMessage());
                    $this->addError($e->getMessage());
                }
            }
        }

        $this->_redirectReferer();
    }

    /**
     * Create/Edit task action
     *
     * @param null $taskId
     */
    public function taskEdit($taskId = null)
    {
        $this->initialize();

        $this->addJs('js/task/edit.js');

        $this->addContentBlock('edit.task.form', 'task/edit');
        $this->setContentData('edit.task.form', 'title', lang('task_edit'));

        $this->setContentData('edit.task.form', 'task',
            is_null($taskId) ? $this->taskModel : $this->taskModel->load($taskId));

        $this->renderLayout();
    }

    /**
     * Task save action
     */
    public function taskSave()
    {
        $this->initialize();

        $taskId = $this->security->xss_clean($this->input->post('task_id'));
        $this->form_validation->set_rules('title', '"' . lang('title') . '"',
            'trim|required|max_length[255]');
        $this->form_validation->set_rules('description', '"' . lang('description') . '"',
            'trim|required');

        if ($this->form_validation->run() == false) {
            $this->_redirectReferer();
            return;
        }
        $task = $this->taskModel->load($taskId);

        $task->addData([
            'title' => $this->security->xss_clean($this->input->post('title')),
            'description' => $this->security->xss_clean($this->input->post('description'))
        ]);

        try {
            $task->save();
        } catch (Exception $e) {
            log_message('exception', $e->getMessage());
            $this->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        }

        $this->addSuccess(lang('notification_success_task_updated'));
        $this->_redirect('taskList');
    }

    /**
     * Change task status action
     *
     * @param null $taskId
     */
    public function taskChangeStatus($taskId = null)
    {
        $this->initialize();

        if (!$taskId) {
            $this->addError(lang('task_id_required'));
            $this->_redirectReferer();
            return;
        }
        $task = $this->taskModel->load($taskId);
        $task->setData('is_complete', $task->getData('is_complete') ? 0 : 1);

        try {
            $task->save();
        } catch (Exception $e) {
            log_message('exception', $e->getMessage());
            $this->addError($e->getMessage());
            $this->_redirectReferer();
            return;
        }

        $this->addSuccess(lang('notification_success_task_updated'));
        $this->_redirect('taskList');
    }
}
