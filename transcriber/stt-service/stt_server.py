from fastapi import FastAPI, UploadFile, File, Form
from faster_whisper import WhisperModel
import tempfile, os, shutil

MODEL_NAME = os.environ.get("STT_MODEL", "base")
COMPUTE    = os.environ.get("STT_COMPUTE", "int8")

app = FastAPI()
model = WhisperModel(MODEL_NAME, compute_type=COMPUTE)

@app.post("/transcribe")
async def transcribe(audio: UploadFile = File(...), language: str | None = Form(None)):
    suffix = os.path.splitext(audio.filename or "")[1] or ".webm"
    with tempfile.NamedTemporaryFile(suffix=suffix, delete=False) as tmp:
        shutil.copyfileobj(audio.file, tmp)
        temp_path = tmp.name

    segments, info = model.transcribe(temp_path, language=language, vad_filter=True, beam_size=1)
    text = " ".join(seg.text for seg in segments).strip()
    os.remove(temp_path)
    return {"text": text}
