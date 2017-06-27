<?php

namespace Aweika\LaravelUeditor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Aweika\LaravelUeditor\Uploader;

class HandleController extends Controller
{
    public function index()
    {
        $CONFIG = config('aweika-laravel-ueditor.upload');

        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  self::json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                /**
                 * 上传附件和上传视频
                 * User: Jinqn
                 * Date: 14-04-09
                 * Time: 上午10:17
                 */
                //include "Uploader.class.php";

                /* 上传配置 */
                $base64 = "upload";
                switch (htmlspecialchars($_GET['action'])) {
                    case 'uploadimage':
                        $config = array(
                            "pathFormat" => $CONFIG['imagePathFormat'],
                            "maxSize" => $CONFIG['imageMaxSize'],
                            "allowFiles" => $CONFIG['imageAllowFiles']
                        );
                        $fieldName = $CONFIG['imageFieldName'];
                        break;
                    case 'uploadscrawl':
                        $config = array(
                            "pathFormat" => $CONFIG['scrawlPathFormat'],
                            "maxSize" => $CONFIG['scrawlMaxSize'],
                            "allowFiles" => $CONFIG['scrawlAllowFiles'],
                            "oriName" => "scrawl.png"
                        );
                        $fieldName = $CONFIG['scrawlFieldName'];
                        $base64 = "base64";
                        break;
                    case 'uploadvideo':
                        $config = array(
                            "pathFormat" => $CONFIG['videoPathFormat'],
                            "maxSize" => $CONFIG['videoMaxSize'],
                            "allowFiles" => $CONFIG['videoAllowFiles']
                        );
                        $fieldName = $CONFIG['videoFieldName'];
                        break;
                    case 'uploadfile':
                    default:
                        $config = array(
                            "pathFormat" => $CONFIG['filePathFormat'],
                            "maxSize" => $CONFIG['fileMaxSize'],
                            "allowFiles" => $CONFIG['fileAllowFiles']
                        );
                        $fieldName = $CONFIG['fileFieldName'];
                        break;
                }

                /* 生成上传实例对象并完成上传 */
                $up = new Uploader($fieldName, $config, $base64);

                /**
                 * 得到上传文件所对应的各个参数,数组结构
                 * array(
                 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
                 *     "url" => "",            //返回的地址
                 *     "title" => "",          //新文件名
                 *     "original" => "",       //原始文件名
                 *     "type" => ""            //文件类型
                 *     "size" => "",           //文件大小
                 * )
                 */

                /* 返回数据 */
                $result = self::json_encode($up->getFileInfo());

                //$result = include("action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
            /* 列出文件 */
            case 'listfile':
                /**
                 * 获取已上传的文件列表
                 * User: Jinqn
                 * Date: 14-04-09
                 * Time: 上午10:17
                 */
                //include "Uploader.class.php";

                /* 判断类型 */
                switch ($_GET['action']) {
                    /* 列出文件 */
                    case 'listfile':
                        $allowFiles = $CONFIG['fileManagerAllowFiles'];
                        $listSize = $CONFIG['fileManagerListSize'];
                        $path = $CONFIG['fileManagerListPath'];
                        break;
                    /* 列出图片 */
                    case 'listimage':
                    default:
                        $allowFiles = $CONFIG['imageManagerAllowFiles'];
                        $listSize = $CONFIG['imageManagerListSize'];
                        $path = $CONFIG['imageManagerListPath'];
                }
                $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

                /* 获取参数 */
                $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
                $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
                $end = $start + $size;

                /* 获取文件列表 */
                $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
                $files = self::getfiles($path, $allowFiles);
                if (!count($files)) {
                    return self::json_encode(array(
                        "state" => "no match file",
                        "list" => array(),
                        "start" => $start,
                        "total" => count($files)
                    ));
                }

                /* 获取指定范围的列表 */
                $len = count($files);
                for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
                    $list[] = $files[$i];
                }
                //倒序
                //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
                //    $list[] = $files[$i];
                //}

                /* 返回数据 */
                $result = self::json_encode(array(
                    "state" => "SUCCESS",
                    "list" => $list,
                    "start" => $start,
                    "total" => count($files)
                ));

                break;

            /* 抓取远程文件 */
            case 'catchimage':
                /**
                 * 抓取远程图片
                 * User: Jinqn
                 * Date: 14-04-14
                 * Time: 下午19:18
                 */
                set_time_limit(0);
                //include("Uploader.class.php");

                /* 上传配置 */
                $config = array(
                    "pathFormat" => $CONFIG['catcherPathFormat'],
                    "maxSize" => $CONFIG['catcherMaxSize'],
                    "allowFiles" => $CONFIG['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $CONFIG['catcherFieldName'];

                /* 抓取远程图片 */
                $list = array();
                if (isset($_POST[$fieldName])) {
                    $source = $_POST[$fieldName];
                } else {
                    $source = $_GET[$fieldName];
                }
                foreach ($source as $imgUrl) {
                    $item = new Uploader($imgUrl, $config, "remote");
                    $info = $item->getFileInfo();
                    array_push($list, array(
                        "state" => $info["state"],
                        "url" => $info["url"],
                        "size" => $info["size"],
                        "title" => htmlspecialchars($info["title"]),
                        "original" => htmlspecialchars($info["original"]),
                        "source" => htmlspecialchars($imgUrl)
                    ));
                }

                /* 返回抓取数据 */
                $result = self::json_encode(array(
                    'state'=> count($list) ? 'SUCCESS':'ERROR',
                    'list'=> $list
                ));
                //$result = include("action_crawler.php");
                break;

            default:
                $result = self::json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        return $result;
    }

    public static function json_encode($result){
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                return response()->json($result)->withCallback(request()->input('callback'));
            } else {
                $result = array(
                    'state'=> 'callback参数不合法'
                );
            }
        }
        return response()->json($result);
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private static function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    self::getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
}
