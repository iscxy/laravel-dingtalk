# laravel-dingtalk 中文说明

用于laravel的《钉钉》应用扩展包。


- ### 安装
  ```
  命令行
  ```
- ### 使用方法
  ```
  使用方法
  ```

- ### 错误码

|errCode|errMsg|原因|排查方法|
|:----|:----|:----|:----|
|200001|Http请求错误||检查Http请求的URL|
|200002|将AccessAoken写入Cache缓存失败||检查Cache缓存|
|210001|缺少AppKey或AppSecret|empty($appkey)或empty($appsecret)为空|检查传入的\$appkey和\$appsecret
|210002|无该AppKey项配置||配置中是否有对应AppKey项的配置信息
|210003|返回数据中缺少errcode键名||检查返回值|
|:----|:----|:----|:----|
|:----|:----|:----|:----|
