FROM python:3.11-slim

ENV PYTHONDONTWRITEBYTECODE=1     PYTHONUNBUFFERED=1

WORKDIR /app

RUN apt-get update  && apt-get install -y --no-install-recommends     ffmpeg     libgomp1  && rm -rf /var/lib/apt/lists/*

RUN pip install --no-cache-dir     fastapi     uvicorn[standard]     faster-whisper     python-multipart

COPY . .

EXPOSE 8001

CMD ["uvicorn", "stt_server:app", "--host", "0.0.0.0", "--port", "8001"]
