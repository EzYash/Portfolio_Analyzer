<?php
require "auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portfolio Skill Analyzer</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assests/css/style.css">
</head>

<body>

<header class="topbar">
  <div class="topbar-inner">
    <div class="brand" onclick="showView('analyzer')" role="button" tabindex="0">
      <span class="brand-mark"></span>
      <span class="brand-name">Portfolio Analyzer</span>
    </div>
    <nav class="nav">
      <button class="nav-btn active" id="nav-analyzer" type="button" onclick="showView('analyzer')">Portfolio Analyzer</button>
      <button class="nav-btn" id="nav-career" type="button" onclick="showView('career')">Career Map</button>

      <a href="dashboard.php" class="nav-btn">Dashboard</a>
      <a href="history.php" class="nav-btn">History</a>
      <a href="logout.php" class="nav-btn">Logout</a>
      <button class="nav-btn" type="button" onclick="resetForm()">Reset</button>
    </nav>
  </div>
</header>

<div class="main" style="display:flex; flex-direction:column; align-items:center;">
<div class="background-blur"></div>

<section class="view view-home" id="view-home" hidden>



</section>

<section class="view view-analyzer" id="view-analyzer">

<div class="analyzer-container" style="max-width:700px; margin:40px auto;">
<div class="form-card" style="border:1px solid #e5e7eb; border-radius:8px; padding:20px;">

<div class="form-header">
<h1>Portfolio Skill Analyzer</h1>
<p class="subtitle">Analyze your skills and see how ready you are for your target tech role</p>
</div>

<div class="field-group">
<label for="name">Full Name *</label>
<input id="name" placeholder="Enter your full name" required>
</div>

<div class="field-group">
<label for="email">Email Address *</label>
<input id="email" type="email" placeholder="Enter your email" required>
</div>

<div class="field-group">
<label>Gender</label>
<div class="radio-box">
<div class="radio-group">
<label><input type="radio" name="gender" value="male"> Male</label>
<label><input type="radio" name="gender" value="female"> Female</label>
<label><input type="radio" name="gender" value="other"> Other</label>
</div>
</div>
</div>

<div class="field-group">
<label for="role">Select Role *</label>
<select id="role" name="role" required>
  <option value="" disabled selected>Select Role</option>
  <option>Full Stack Developer</option>
  <option>Frontend Developer</option>
  <option>Backend Developer</option>
  <option>Software Engineer</option>
  <option>Data Scientist</option>
  <option>Machine Learning Engineer</option>
  <option>AI Engineer</option>
  <option>DevOps Engineer</option>
  <option>Cybersecurity Analyst</option>
  <option>Game Developer</option>
</select>
</div>

<div class="field-group">
<label for="company">Target Company *</label>
<input id="company" placeholder="e.g., Google, Microsoft, Amazon" required>
</div>

<div class="field-group">
<label for="skills">Skills *</label>
<input id="skills" placeholder="HTML, CSS, JavaScript..." required>
</div>

<div class="field-group">
<label for="projects">Projects (comma separated)</label>
<input id="projects" placeholder="Portfolio Website, Chat App">
</div>

<div class="field-group">
<label for="experience">Years of Experience</label>
<input id="experience" type="number" min="0">
</div>

<div class="field-group">
<label for="experienceMonths">Experience (Months)</label>
<input id="experienceMonths" type="number" min="0" max="11">
</div>

<div class="field-group">
<label for="salaryExpectation">Salary Expectation (₹ LPA)</label>
<input id="salaryExpectation" type="number" min="0" step="0.5">
</div>

<label class="check">
<input type="checkbox" id="internship">
Internship Experience
</label>

<button class="analyze-btn" onclick="analyze()">Analyze Portfolio</button>

</div>

<div class="result-card" id="result">
<h3>Results Dashboard</h3>
<p>Your portfolio analysis will appear here after you click "Analyze Portfolio".</p>

<div id="dashboard" class="dashboard"></div>

</div>
</div>
</section>

<section class="view view-career" id="view-career" hidden>
<div class="wide-card">
<div class="wide-head">
<h2>Career Map</h2>
<p class="subtitle">Pick your target role and get a step-by-step path.</p>
</div>

<div class="career-controls">
<label for="careerRole">Target Role</label>

<select id="careerRole">
<option value="" disabled selected>Select Role</option>
<option>Full Stack Developer</option>
<option>Frontend Developer</option>
<option>Backend Developer</option>
<option>Data Scientist</option>
<option>Machine Learning Engineer</option>
<option>AI Engineer</option>
<option>DevOps Engineer</option>
<option>Cybersecurity Analyst</option>
<option>Game Developer</option>
</select>

<button class="analyze-btn" type="button" onclick="generateCareerMap()">Generate Career Map</button>
</div>

<div id="careerMap" class="career-map">
<p class="helper">Select a role above to see a career map.</p>
</div>

</div>
</section>

</div>


<script src="assests/js/script.js"></script>

<!-- Chatbot UI -->
<style>
#chatToggle{
 position:fixed;
 bottom:20px;
 right:20px;
 background:#4f46e5;
 color:white;
 border:none;
 border-radius:50%;
 width:56px;
 height:56px;
 font-size:22px;
 cursor:pointer;
 box-shadow:0 6px 16px rgba(0,0,0,0.3);
}

#chatBox{
 position:fixed;
 bottom:90px;
 right:20px;
 width:320px;
 height:420px;
 background:white;
 border-radius:10px;
 box-shadow:0 10px 25px rgba(0,0,0,0.25);
 display:none;
 flex-direction:column;
 overflow:hidden;
 font-family:Poppins, sans-serif;
}

#chatHeader{
 background:#4f46e5;
 color:white;
 padding:10px;
 display:flex;
 justify-content:space-between;
 align-items:center;
}

#chatMessages{
 flex:1;
 padding:10px;
 overflow-y:auto;
 font-size:14px;
 color:black;
}

.chatMsg{
 margin-bottom:8px;
}

.userMsg{
 text-align:right;
 color:black;
}

.botMsg{
 text-align:left;
 color:black;
}

#chatInput{
 display:flex;
 border-top:1px solid #eee;
}

#chatInput input{
 flex:1;
 border:none;
 padding:8px;
 font-size:14px;
 color:white;
 background:#4f46e5;
}

#chatInput button{
 border:none;
 background:#4f46e5;
 color:white;
 padding:8px 12px;
 cursor:pointer;
}
</style>

<button id="chatToggle" onclick="toggleChat()">💬</button>

<div id="chatBox">
<div id="chatHeader">
<span>AI Assistant</span>
<button onclick="toggleChat()" style="background:none;border:none;color:white;font-size:16px;cursor:pointer;">✕</button>
</div>

<div id="chatMessages"></div>

<div id="chatInput">
<input id="chatText" placeholder="Ask something...">
<button onclick="sendChat()">Send</button>
</div>
</div>

<script>
function toggleChat(){
 const box=document.getElementById("chatBox");
 box.style.display = box.style.display==="flex" ? "none" : "flex";
}

async function sendChat(){

 const input=document.getElementById("chatText");
 const msg=input.value.trim();
 if(!msg) return;

 const messages=document.getElementById("chatMessages");

 messages.innerHTML += `<div class="chatMsg userMsg"><b>You:</b> ${msg}</div>`;
 input.value="";

 const res = await fetch("http://localhost:5001/chat_api",{
  method:"POST",
  headers:{
   "Content-Type":"application/json"
  },
  body:JSON.stringify({message:msg})
 });

 const data = await res.json();

 let reply="Error contacting AI";

 if(data.output_text){
   reply=data.output_text;
 }
 else if(data.output && data.output[0] && data.output[0].content){
   reply=data.output[0].content[0].text;
 }

 messages.innerHTML += `<div class="chatMsg botMsg"><b>AI:</b> ${reply}</div>`;
 messages.scrollTop = messages.scrollHeight;
}
</script>

</body>
</html>
