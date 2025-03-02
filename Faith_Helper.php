<?php
// 用來存放 OpenAI API Key，請根據您的需求替換
$openai_api_key = 'OpenAI API Key';

// 使用 OpenAI API - php

// 從前端接收 JSON 格式的輸入數據
$input_data = json_decode(file_get_contents('php://input'), true);
$user_message = $input_data['message']; // 獲取使用者輸入的消息
$chat_history = $input_data['history']; // 獲取聊天歷史記錄

// 合併之前的對話歷史（如果有）
$messages = [];

// 加入系統提示
$messages[] = ['role' => 'system', 'content' => '
你是信仰小幫手
# C (Context, 背景)
你是一位專精於聖經解釋的人工智慧助手，擅長引用經文，並根據聖經的原則提供深入的神學見解和實際應用建議。你的目標是幫助用戶更深入理解聖經教導，並在生活中實踐這些教導。
# O (Objective, 目標)
你的任務是解釋聖經經文的含義，包括：
提供經文的歷史文化背景。
解釋經文的屬靈意義和神學觀點。
提供具體的現代生活應用建議。
結合聖經原則，幫助用戶在愛神、愛人、順服、饒恕等方面成長。
# S (Style, 風格)
你的回答應該：
溫暖且具啟發性：表達時充滿愛心與關懷，像一位耐心的牧師或靈性導師。
簡單清晰：讓任何程度的讀者都能理解，避免複雜的神學術語。
具體實用：不僅提供知識，還包括生活應用的具體步驟。
# T (Tone, 語氣)
語氣應該謙卑、鼓勵、富有信心，幫助用戶感到被支持並獲得啟發。
# A (Audience, 受眾)
主要受眾是希望學習和理解聖經的普通讀者，包括信仰的初學者和希望更深入研究聖經的基督徒。他們可能遇到靈性疑問，或希望將聖經的教導應用到日常生活中。
# R (Response, 回應)
每次回應應包含以下結構：
經文引用：清楚列出經文：包括經文的出處和內容，使用權威聖經版本（例如新譯本、和合本、ESV、NIV、馬太福音 5:16）。
歷史文化背景：
提供經文當時的歷史、文化、地理背景，幫助讀者理解經文在原始語境中的含義。
參考經典神學著作和教會傳統，確保解釋符合正統教義。
屬靈意義與神學觀點：
基於核心基督教教義進行解釋，強調三位一體、基督的救贖計劃、以及聖經整體敘事的脈絡。
引用相關經文（例如舊約預表與新約應驗），以整全方式解釋經文。
現代生活應用：
具體描述如何將經文的教導應用於信徒的日常生活，如愛神、愛人、饒恕、信心等方面。
提供簡單可行的步驟，幫助讀者活出基督徒生命，例如家庭、職場、教會服事中的實際行動。
鼓勵與盼望：
用溫暖、啟發性的語言，幫助讀者感到被支持，並提醒神的應許與恩典。
強調信徒在基督裡的身份，鼓勵堅守信仰並依靠神的力量。
正統保障策略：
明確教義框架：解釋內容需與基督教核心教義一致，避免異端解釋。
參與信仰群體：建議讀者參與教會或尋求牧者指導，以核實和深化理解。
敏感話題警告：對於爭議性或難解的經文，提醒讀者耐心禱告尋求神的帶領，同時尋求靈性導師的幫助。
用普通文本格式回答
用正體中文回答，使用臺灣特有的慣用語和口語表達，讓交流更自然。
'];

foreach ($chat_history as $message) {
    // 將聊天歷史中的每條消息轉換為 OpenAI API 所需的格式
    $messages[] = ['role' => $message['sender'] == 'user' ? 'user' : 'assistant', 'content' => $message['text']];
}
// 將使用者最新的消息添加到消息數組中
$messages[] = ['role' => 'user', 'content' => $user_message];





// 發送請求到 OpenAI API
$ch = curl_init(); // 初始化 cURL
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions'); // 設定 API 請求的 URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 設定返回結果為字串
curl_setopt($ch, CURLOPT_POST, true); // 設定請求方法為 POST
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json', // 設定請求頭為 JSON 格式
    'Authorization: Bearer ' . $openai_api_key, // 設定 API Key 驗證
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'gpt-4o-mini',  // 選擇使用的模型（請根據需求替換）
    'messages' => $messages, // 傳送消息數組
    'max_tokens' => 3000 // 設定最大生成的 token 數
]));

$response = curl_exec($ch); // 執行 cURL 請求並獲取回應
curl_close($ch); // 關閉 cURL 會話

// 解析並返回回應
$response_data = json_decode($response, true); // 將回應解析為 JSON 格式
$bot_reply = $response_data['choices'][0]['message']['content'] ?? '抱歉，發生了錯誤。'; // 獲取機器人回覆，若出錯則返回預設訊息

// 返回回應
echo json_encode(['reply' => $bot_reply]); // 將機器人回覆以 JSON 格式返回給前端
?>
