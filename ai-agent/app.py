import os
import json
import re
from typing import List, Optional
from fastapi import FastAPI, HTTPException, Header, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from dotenv import load_dotenv
import google.generativeai as genai

load_dotenv()
GEMINI_KEY = os.getenv("GEMINI_API_KEY")
SHARED_TOKEN = os.getenv("FASTAPI_AGENT_SHARED_SECRET")
if not GEMINI_KEY:
    raise RuntimeError("GEMINI_API_KEY not set")

genai.configure(api_key=GEMINI_KEY)
MODEL_NAME = "gemini-1.5-flash"

app = FastAPI(title="AlignPath AI Agent")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class CareerPath(BaseModel):
    title: str
    description: str

class CareerResponse(BaseModel):
    career_paths: List[CareerPath] = Field(default_factory=list)

class Quest(BaseModel):
    path_name: str
    title: str
    subtitle: str
    difficulty: str
    duration: str

class QuestResponse(BaseModel):
    quests: List[Quest] = Field(default_factory=list)

class InterestsIn(BaseModel):
    interests: str

class CareerIn(BaseModel):
    career: str

def extract_json(text: str):
    """Try to robustly pull JSON out of Gemini response."""
    try:
        return json.loads(text)
    except Exception:
        pass
    fence = re.search(r"```json\s*(\{.*?\})\s*```", text, flags=re.S)
    if fence:
        try:
            return json.loads(fence.group(1))
        except Exception:
            pass
    blob = re.search(r"(\{.*\})", text, flags=re.S)
    if blob:
        try:
            return json.loads(blob.group(1))
        except Exception:
            pass
    return None

def call_gemini(prompt: str) -> str:
    model = genai.GenerativeModel(MODEL_NAME)
    resp = model.generate_content(prompt)
    if not getattr(resp, "text", None):
        raise HTTPException(status_code=502, detail="Gemini returned empty response")
    return resp.text

def verify_shared_token(header_val: Optional[str]):
    if not SHARED_TOKEN:
        return
    if header_val != f"Bearer {SHARED_TOKEN}":
        raise HTTPException(status_code=401, detail="Unauthorized")

@app.get("/health")
def health():
    return {"ok": True}

@app.post("/recommend-careers", response_model=CareerResponse)
async def recommend_careers(body: InterestsIn, authorization: Optional[str] = Header(None)):
    verify_shared_token(authorization)

    prompt = f"""
You are a careful JSON-only API.

The user profile includes skills, interests, values, and career preferences combined in one string.
Profile: {body.interests}

Task:
1) Analyze the profile carefully.
2) Recommend exactly 3 career paths that align with the profile (skills, interests, values, and careers).
3) Keep them diverse.
4) Respond in pure JSON (no prose), matching this schema:

{{
  "career_paths": [
    {{"title": "string", "description": "string"}},
    {{"title": "string", "description": "string"}},
    {{"title": "string", "description": "string"}}
  ]
}}
"""
    text = call_gemini(prompt)
    data = extract_json(text) or {"career_paths": []}
    return CareerResponse(**data)

@app.post("/generate-quests", response_model=QuestResponse)
async def generate_quests(body: CareerIn, authorization: Optional[str] = Header(None)):
    verify_shared_token(authorization)

    prompt = f"""
You are a careful JSON-only API.
Career: "{body.career}"

Task:
Generate exactly 10 beginner-friendly quests with:
- path_name (the career name)
- title (short quest title)
- subtitle (short description of the quest)
- difficulty (easy/medium/hard)
- duration (string like "2 hours" or "30 minutes")
Respond as JSON ONLY, matching this schema:

{{
  "quests": [
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}},
    {{"path_name": "string", "title": "string", "subtitle": "string", "difficulty": "easy|medium|hard", "duration": "string"}}
  ]
}}
"""
    text = call_gemini(prompt)
    data = extract_json(text) or {"quests": []}
    return QuestResponse(**data)
