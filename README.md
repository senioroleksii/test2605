## 🚀 Start Project

```bash
cd test
php artisan serve
```
Open in browser: http://127.0.0.1:8000/upload

for testing use console in google chrome 

start uploading file

stop laravel server to make a fake internet connection failure

start laravel server again to continue uploading

make sure that file is sucessfully loaded in the dirrectory `test/storage/app/private/uploads`

#For demonstration purposes, the delay `const DELAY_MS = 100;` is set at the line `test/resources/views/upload.blade.php:18`
