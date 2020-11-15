<?php
defined('BASEPATH') or exit ('No direct script access allowed');

abstract class BaseController extends CI_Controller
{
    /**
     * @var string
     */
    protected $assetsPath = FCPATH . 'assets';

    /**
     * @var int
     */
    protected $gridPageLimit = 5;

    /**
     * @var CI_Session
     */
    public $session;

    /**
     * @var CI_Security
     */
    public $security;

    /**
     * @var CI_URI
     */
    public $uri;

    /**
     * @var CI_Form_validation
     */
    public $form_validation;

    /**
     * @var CI_Input
     */
    public $input;

    /**
     * @var CI_Pagination
     */
    public $pagination;

    /**
     * @var CI_User_agent
     */
    public $agent;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $headerData = [];

    /**
     * @var array
     */
    protected $footerData = [];

    /**
     * @var array
     */
    protected $contentData = [];

    /**
     * @var array
     */
    protected $contentBlocks = [];

    /**
     * @var string
     */
    protected $renderingBlock;

    /**
     * @var array
     */
    protected $notifications = [
        'error' => [],
        'success' => []
    ];

    /**
     * @param $message
     * @param array $params
     */
    public function addError($message, $params = [])
    {
        $this->notifications['error'][] = vsprintf($message, $params);
    }

    /**
     * @param $message
     * @param array $params
     */
    public function addSuccess($message, $params = [])
    {
        $this->notifications['success'][] = vsprintf($message, $params);
    }

    /**
     * @return array
     */
    public function getContentBlocks()
    {
        return $this->contentBlocks;
    }

    /**
     * Add content block
     * @param $key
     * @param $contentBlock
     * @param int $sortOrder
     */
    public function addContentBlock($key, $contentBlock, $sortOrder = 0)
    {
        $this->contentBlocks[$key] = [
            'sort_order' => $sortOrder,
            'block' => $contentBlock
        ];
    }

    /**
     * Initialize controller data
     *
     * @return $this
     */
    protected function initialize()
    {
        $this->setData('body_class', 'skin-blue sidebar-mini')
            ->setData('form_token_name', $this->security->get_csrf_token_name())
            ->setData('form_token_value', $this->security->get_csrf_hash());

        return $this;
    }

    /**
     * Add custom js to page
     *
     * @param $jsFile
     */
    public function addJs($jsFile)
    {
        if (file_exists($this->assetsPath . DS . $jsFile)) {
            if (!isset($this->headerData['js'])) {
                $this->headerData['js'] = [];
            }

            $this->headerData['js'][] = $jsFile;
        }
    }

    /**
     * Get custom js list
     *
     * @return array|mixed
     */
    public function getJs()
    {
        if (isset($this->headerData['js'])) {
            return $this->headerData['js'];
        }

        return [];
    }

    /**
     * Redirect
     *
     * @param $action
     */
    protected function _redirect($action)
    {
        redirect('/' . $action);
    }

    /**
     * Redirect to url
     *
     * @param $url
     */
    protected function _redirectToUrl($url)
    {
        redirect($url);
    }

    /**
     * Redirect referer
     */
    protected function _redirectReferer()
    {
        redirectReferer();
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if (is_null($key)) {
            return $this->data;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return BaseController
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getContentData($key = null)
    {
        if (is_null($key) && isset($this->contentData[$this->renderingBlock])) {
            return $this->contentData[$this->renderingBlock];
        }

        if (isset($this->contentData[$this->renderingBlock][$key])) {
            return $this->contentData[$this->renderingBlock][$key];
        }

        return null;
    }

    /**
     * @param $blockName
     * @param $key
     * @param $value
     * @return BaseController
     */
    public function setContentData($blockName, $key, $value)
    {
        if (!isset($this->contentData[$blockName])) {
            $this->contentData[$blockName] = [];
        }
        $this->contentData[$blockName][$key] = $value;

        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getHeaderData($key = null)
    {
        if (is_null($key)) {
            return $this->headerData;
        }


        if (isset($this->headerData[$key])) {
            return $this->headerData[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return BaseController
     */
    public function setHeaderData($key, $value)
    {
        $this->headerData[$key] = $value;
        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getFooterData($key = null)
    {
        if (is_null($key)) {
            return $this->footerData;
        }


        if (isset($this->footerData[$key])) {
            return $this->footerData[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @param $value
     * @return BaseController
     */
    public function setFooterData($key, $value)
    {
        $this->footerData[$key] = $value;
        return $this;
    }

    /**
     * Renderer page layout
     */
    public function renderLayout()
    {
        $this->load->view('page/page');
    }

    /**
     * Renderer content
     */
    public function renderContent()
    {
        uasort($this->contentBlocks, function ($a, $b) {
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            }

            return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
        });

        foreach ($this->contentBlocks as $blockName => $block) {
            $this->renderingBlock = $blockName;
            $this->load->view($block['block'], $this->data);
        }
    }

    /**
     * Get Url
     * @param $action
     * @return string
     */
    public function getUrl($action)
    {
        return base_url('/' . $action);
    }

    /**
     * Renderer sidebar
     */
    public function loadSidebar()
    {
        return $this;
    }

    /**
     * Destructor
     */
    public function destruct()
    {
        foreach ($this->form_validation->error_array() as $error) {
            $this->addError($error);
        }

        if (count($this->notifications['error']) || count($this->notifications['success'])) {
            $this->session->set_flashdata('notification', $this->notifications);
        }
    }

    /*********************** Actions ***********************/

    /**
     * Page not found
     */
    public function pageNotFound()
    {
        $this->setHeaderData('page_title', lang('not_found_title'));
        $this->setHeaderData('ignore_header', true);
        $this->addContentBlock('not_found_content', 'common/404');
        $this->setData('content_class', 'not-found');
        $this->renderLayout();
    }

    /**
     * Add search text filter
     *
     * @param PS_Collection $collection
     * @param array $columnNames
     * @param null $gridName
     */
    protected function prepareGridCollection($collection, $columnNames, $gridName = null)
    {
        $searchText = $this->security->xss_clean($this->input->post('search_text'));
        if ($searchText) {
            $collection->addFieldToFilter(
                $columnNames,
                array_fill(0, count($columnNames), ['like' => $searchText])
            );
        }

        if ($gridName) {
            $this->setContentData($gridName, 'search_text', $searchText);
        }
    }

    /**
     * Initialize pagination
     *
     * @param $gridLink
     * @param $collectionCount
     * @param null $gridPageLimit
     * @return $this
     */
    function initializePagination($gridLink, $collectionCount, $gridPageLimit = null)
    {
        $this->load->library('pagination');
        $config = [
            'base_url' => $gridLink,
            'total_rows' => $collectionCount,
            'per_page' => !is_null($gridPageLimit) ? $gridPageLimit : $this->gridPageLimit,
            'use_page_numbers' => true,
            'num_links' => 5,
            'full_tag_open' => '<nav><ul class="pagination">',
            'full_tag_close' => '</ul></nav>',
            'first_tag_open' => '<li class="arrow">',
            'first_link' => lang('pagination_first'),
            'first_tag_close' => '</li>',
            'prev_link' => lang('pagination_previous'),
            'prev_tag_open' => '<li class="arrow">',
            'prev_tag_close' => '</li>',
            'next_link' => lang('pagination_next'),
            'next_tag_open' => '<li class="arrow">',
            'next_tag_close' => '</li>',
            'cur_tag_open' => '<li class="active"><a href="#">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'last_tag_open' => '<li class="arrow">',
            'last_link' => lang('pagination_last'),
            'last_tag_close' => '</li>'
        ];

        $this->pagination->initialize($config);

        return $this;
    }
}
