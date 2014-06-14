##这是什么？

* 这是一个开源的中文的新浪微博网页客户端
* 本代码最先由Tim Yang [http://timyang.net/](http://timyang.net/))修改，后来由 <a href="http://www.weibo.com/liruqi">@liruqi</a> 修改。
* 感谢Dabr的作者 @davidcarrington 和给他灵感的 @whatleydude ，以及奶瓶腿的作者<a href="http://weibo.com/NetPuter">@NetPuter</a>!

##安装

1. 上传至php服务器
2. 复制config.sample.php并按示例进行配置
3. 在微博开放平台修改应用的授权回调页
4. 根据需要修改伪静态设置

###Nginx伪静态示例

	server {
		listen   80;
		server_name  asuwish.cc;
		index index.php;
		error_log  /var/log/nginx/asuwish.error.log;
		access_log  /var/log/nginx/asuwish.access.log;
		index  index.php;
		if (!-f $request_filename) {
			rewrite ^/(.*)$ /index.php?q=$1 last;
		}
		root   /data/www/liruqi/liruqi.info;
		location ~ \.php$ {
			fastcgi_pass   127.0.0.1:9000;
			fastcgi_index  index.php;
			fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
			include fastcgi_params;
		}
	}

###新浪app engine伪静态示例
	handle:
	  - rewrite: if(!is_dir() && !is_file() && path ~ "/(.*)") goto "/index.php?q=$1"
