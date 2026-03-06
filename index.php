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
      <button class="nav-btn active" id="nav-home" type="button" onclick="showView('home')">Home</button>
      <button class="nav-btn" id="nav-analyzer" type="button" onclick="showView('analyzer')">Portfolio Analyzer</button>
      <button class="nav-btn" id="nav-career" type="button" onclick="showView('career')">Career Map</button>

      <a href="dashboard.php" class="nav-btn">Dashboard</a>
      <a href="history.php" class="nav-btn">History</a>
      <a href="logout.php" class="nav-btn">Logout</a>
    </nav>
  </div>
</header>

<div class="main" style="display:flex; flex-direction:column; align-items:center;">
<div class="background-blur"></div>

<section class="view view-home" id="view-home">



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
<label>Select Roles *</label>

<details class="roles-dropdown" style="border:1px solid #e5e7eb; border-radius:6px; padding:10px;">
<summary>Select Role(s)</summary>

<div class="checkbox-roles" style="display:flex; flex-direction:column; gap:6px; margin-top:8px;">

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Full Stack Developer" id="role-fullstack">
<span>Full Stack Developer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Frontend Developer" id="role-frontend">
<span>Frontend Developer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Backend Developer" id="role-backend">
<span>Backend Developer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Software Engineer" id="role-software">
<span>Software Engineer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Data Scientist" id="role-datasci">
<span>Data Scientist</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Machine Learning Engineer" id="role-ml">
<span>Machine Learning Engineer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="AI Engineer" id="role-ai">
<span>AI Engineer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="DevOps Engineer" id="role-devops">
<span>DevOps Engineer</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Cybersecurity Analyst" id="role-cyber">
<span>Cybersecurity Analyst</span>
</label>

<label class="role-option" style="display:flex; justify-content:space-between; align-items:center;">
<input type="checkbox" name="roles" value="Game Developer" id="role-game">
<span>Game Developer</span>
</label>

</div>

</details>

<p class="helper">You can select multiple roles.</p>
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

</body>
</html>