# Upyun upload plugin


## migrate db from http images to https(protocal-relative url)

```sql
UPDATE `et_post`
SET `content` = REPLACE(`content`, "http://<bucket>.b0.upaiyun.com", "//<bucket>.b0.upaiyun.com")
WHERE `content` LIKE "%http://<bucket>.b0.upaiyun.com%"
```
