<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Загрузка файла</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<h2>Загрузить большой файл</h2>

<input type="file" id="fileInput" />
<button onclick="uploadFile()">Загрузить</button>

<script>
    const CHUNK_SIZE = 100 * 1024;
    const DELAY_MS = 0; // fixme make 0 after testing

    async function uploadFile() {
        const file = document.getElementById('fileInput').files[0];
        if (!file) return alert('Выберите файл');

        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            const start = chunkIndex * CHUNK_SIZE;
            const end = Math.min(start + CHUNK_SIZE, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('fileName', file.name);
            formData.append('chunkIndex', chunkIndex);
            formData.append('totalChunks', totalChunks);

            let uploaded = false;
            while (!uploaded) {
                try {
                    const response = await fetch('/upload-chunk', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    if (result.success) {
                        console.log(`Чанк ${chunkIndex + 1}/${totalChunks} загружен`);
                        uploaded = true;
                    } else {
                        console.warn(`Ошибка загрузки чанка ${chunkIndex}, повтор...`);
                    }
                } catch (err) {
                    console.warn(`Ошибка сети на чанке ${chunkIndex}, повтор через 1с...`);
                    await new Promise(res => setTimeout(res, 1000));
                }
            }

            await new Promise(res => setTimeout(res, DELAY_MS));
        }

        alert('Файл успешно загружен!');
    }
</script>

</body>
</html>
