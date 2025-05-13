import sys
from openai import OpenAI
import os
from dotenv import load_dotenv
import base64
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys

load_dotenv(dotenv_path="../.env")

if len(sys.argv) < 2:
    sys.exit(1)
keyword = sys.argv[1]

openAI_API_KEY = os.getenv("OPENAI_API_KEY")
client = OpenAI(api_key=openAI_API_KEY)

options = webdriver.ChromeOptions()
driver = webdriver.Chrome(options=options)
driver.get("https://www.mercari.com/jp/")
time.sleep(2)

search_box = driver.find_element(By.CSS_SELECTOR, "input[placeholder='なにをお探しですか？']")
search_box.send_keys(keyword)
search_box.send_keys(Keys.RETURN)

time.sleep(5)

screenshotfile = "mercari_result.png"
w = driver.execute_script("return document.body.scrollWidth;")
h = driver.execute_script("return document.body.scrollHeight;")
driver.set_window_size(w,3*h)
driver.save_screenshot(screenshotfile)
driver.quit()

with open(screenshotfile, "rb") as f:
    image = base64.b64encode(f.read()).decode("utf-8")

response = client.chat.completions.create(
    model="gpt-4o",
    messages=[
        {
            "role": "system",
            "content": "あなたは画像の中のある単一商品を分析して、価格を読み取るAIです。"
        },
        {
            "role": "user",
            "content": [
                {
                    "type": "image_url",
                    "image_url": {"url": f"data:image/png;base64,{image}"}
                },
                {
                    "type": "text",
                    "text": (
                        "この画像に表示されている商品のうち、最も多く出現している商品の価格を見て、"
                        "その商品の平均価格を日本円で数字で出力してください。"
                        "出力は平均価格のみ、1行で、余計な説明なしでお願いします。"
                    )
                }
            ]
        }
    ],
    max_tokens=300
)

print(response.choices[0].message.content)