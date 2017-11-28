# 臺北市政府i-Voting網路投票系統

### 1.專案說明
臺北市政府為推動i-Voting網路投票系統開放原始碼政策，以提升程式品質、系統功能精進，達到程式共享，縮短開發時程目的。
  
### 2.開發環境

2-1.部屬環境

|項目|版本|
|--- |---|
|CentOS|7+|
|Apache|2.4+|
|Mariadb|5.5+|
|PHP|5.4+|

2-2.第三方函數庫

| 項次 | 函式名稱 | 功能說明 | 授權 | 下載網址 |
| ---- | ----     | ----    | ---- | ----    |
|  1   | Securimage| 圖形驗證功能| BSD License | https://www.phpcaptcha.org/ |

2-3. 其他說明
* php需安裝php-xml, php-pdo, php-mbstring
* 投票模組須使用到編輯器，建議可使用JCE編輯工具 下載網址 https://www.joomlacontenteditor.net/downloads/editor/core

### 3.功能項目

* 投票結果顯示機制(surveyforce)
* 訊息通知機制(surveyforce)
* 投票查詢驗證機制(surveyforce)
* 歷史投票結果專區(surveyforce)
* 投票內嵌機制(surveyforce)  
* 首頁重點宣傳區(surveyforce)
* 計數器功能(counter)
* 選單功能(sfmenu)
* 返回上一頁功能(return)
* 捲軸功能(scroll)
* 網站地圖(sitmap)

### 4.安裝說明

 * 作業系統建議安裝CentOS 下載點 https://www.centos.org/download/ 
 * 確認php版本是否為5.4含以上版本。
 * 安裝JOOMLA 3.7.3版本 下載點 https://downloads.joomla.org/zh/cms/joomla3/3-7-3/ 
 * 將此Repo clone 或者下載至本地端的 administraror路徑 compoents路徑 languages路徑及plugins路徑。
 * 下載ivoting-schema.sql檔案，將其新增至資料庫。
	
### 5.授權說明

  臺北市政府i-Voting網路投票系統原始碼採用GPL-2.0+授權條款。詳細請見LICENSE與NOTICE檔
