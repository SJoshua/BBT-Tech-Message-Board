# BBT-Tech-Message-Board
这是2018级[百步梯](https://www.100steps.net/)技术部的第三次部门任务，即制作一个留言板。

## Features
* 注册、登陆与注销
* 发送留言
* 修改与删除留言
* 防XSS与SQL注入

## Configuration
1. 以 `config-sample.php` 为例，在 `config.php` 中填写数据库名及连接凭证。
2. 运行 `install.php` 以创建保存用户信息及留言数据的表。

## To-do
* 写CSS
* 写JS (AJAX)

## Note
现在只是能用，代码并没有理得很清楚。

之前都是直接在VPS上写，这次在本地的WSL配置了Apache2、PHP、MySQL和phpMyAdmin。

在WSL配置的话要在 `/etc/apache2/apache2.conf` 里加一句 `AcceptFilter http none`。

## About
平时很少写Web的东西，说起来小时候做过一个[小熊闯关秀](http://sforest.in/game/)，现在好像还能玩的样子。其余大多数时候都被我拿Lua糊弄过去了，但这样下去也不是办法，该学的东西还是要学的。

从这次任务布置下来已经过了六周，虽然之前有断续地写几个文件，但现在这份代码是 `Dec 11th, 2018` 晚上才写的。现在反思过去的六周，也并非忙到无法挤出一个晚上来完成这样一个任务，只是一味地在往后拖延而已。觉得这只是一个训练，完成与否只影响自己，而没有考虑到部门里学长和同学们的感受。

接下来的部门任务将更认真地对待，在合作开发时会努力去配合他人。

尽管稍显贪心，但也想尽力去尝试，不愿中途举旗投降。