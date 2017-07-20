臺北市政府i-Voting網路投票系統

1.  專案說明
    臺北市政府為推動i-Voting網路投票系統開放原始碼政策，以提升程式品質、系統功能精進，達到程式共享，縮短開發時程目的。
    
2.	開發環境

	2-1.	部屬環境

	|項目|版本|
	|---|---|
	|CentOS|7+|
	|Apache|2.4+|
	|Mariadb|5.5+|
	|PHP|5.4+|

	2-2.	第三方函數庫

	|項目|版本|
	|---|---|
	|securimage||
	

  
	2-3. 其他說明
	
	php需安裝php-xml, php-pdo, php-mbstring
3.	功能項目

	3-1.  投票結果顯示機制
 
	3-2   訊息通知機制
 
	3-3.  投票查詢驗證機制
	
  	3-4.  歷史投票結果專區
 
	3-5.   投票內嵌機制  
 
	3-6.  首頁重點宣傳區
	
4.	安裝(請先安裝JOOMLA 3.4.8版本)
	
	4-1.  將此Repo clone 或者下載至本地端的 administraror路徑 compoents路徑 languages路徑及plugins路徑
 
	4-2.   依照資料庫schema檔案,將其新增置資料庫
	
5.	授權說明

        臺北市政府i-Voting網路投票系統原始碼採用GPL-2.0+授權條款。詳細請見LICENSE與NOTICE檔案。
