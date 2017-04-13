# laravel-ueditor
ueditor for laravel

============================

##使用方法：

1. composer安装
  ```shell
  composer require aweika/laravel-ueditor
  ```
2. 在`config/app.php`中的`providers`的数组中添加
    ```php
    Aweika\LaravelUeditor\LaravelUeditorServiceProvider::class,
    ```
   在`config/app.php`中的`aliases`的数组中添加
   ```php
   'LaravelUeditor' => Aweika\LaravelUeditor\LaravelUeditor::class,
   ```
3. 执行下面命令
  ```shell
  php artisan vendor:publish --provider="Aweika\LaravelUeditor\LaravelUeditorServiceProvider" --tag=first
  ```
  会生成配置文件`config/aweika-laravel-ueditor.php`
  修改其中的`package_path`这个参数，配置ueditor的资源文件生成的目录。
  此参数相对于public目录，比如默认值为`aweika-larvel-ueditor`，则会将编辑器的资源文件生成到`public/aweika-laravel-ueditor`目录中。
  
4. 执行下面命令
  ```shell
  php artisan vendor:publish --provider="Aweika\LaravelUeditor\LaravelUeditorServiceProvider" --tag=second
  ```
  会按照上一步设置的路径生成资源文件和一个组件view。
  
5. 打开`routes/web.php`在适当的位置添加如下代码来设置编辑器上传相关操作所需要的路由。
  ```php
  \LaravelUeditor::routes();
  ```
6. 在需要使用编辑器的view文件中调用组件，比如表单控件的名称为`element`。
  * 简单的调用
  ```php
  @component('aweika-laravel-ueditor.components.ueditor', ['field'=>'element'])
  @endcomponent
  ```

  * 带默认值的调用
  ```php
  @component('aweika-laravel-ueditor.components.ueditor', ['field'=>'element', 'content'=>'default content'])
  @endcomponent
  ```

  * 带自定义脚本的调用
  ```php
  @php
      $csrf_token = csrf_token();
      $custom_script = <<<EOD
      <script type="text/javascript">
          var ue = UE.getEditor('content', {
              initialFrameHeight:300,
              autoHeightEnabled: false,
              autoFloatEnabled: false,
              wordCount:false,
              elementPathEnabled:false,
              toolbars: [
                      ['source', 'undo', 'redo', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', 'simpleupload', 'fullscreen']
                  ]
          });

          ue.ready(function() {
              ue.execCommand('serverparam', '_token', '$csrf_token');
          });
      </script>
EOD;
  @endphp
  @component('aweika-laravel-ueditor.components.ueditor', ['field'=>'element', 'content'=>'default content', 'custom_script'=>$custom_script])
  @endcomponent
  ```