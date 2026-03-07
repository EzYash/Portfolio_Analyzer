
# This file should be renamed to `chat_api.py` and run with Python Flask instead of PHP.
#
# Python version of the chatbot API (replace this file with chat_api.py and run with Python)

from flask import Flask, request, jsonify
import requests
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

API_KEY = ""

@app.route("/chat_api", methods=["POST"])
def chat_api():
    data = request.get_json()
    message = data.get("message", "")

    if not message:
        return jsonify({"error": "No message"}), 400

    payload = {
        "model": "llama3-8b-8192",
        "messages": [
            {
                "role": "user",
                "content": message
            }
        ]
    }

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {API_KEY}"
    }

    response = requests.post(
        "https://api.groq.com/openai/v1/chat/completions",
        json=payload,
        headers=headers
    )

    return jsonify(response.json())

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5001, debug=True)