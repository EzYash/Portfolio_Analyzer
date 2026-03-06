const roleSkills = {

"Frontend Developer": ["html","css","javascript","react","bootstrap","tailwind","git"],

"Backend Developer": ["node","express","php","mysql","api","mongodb","authentication"],

"Full Stack Developer": ["html","css","javascript","node","react","mysql","git","api"],

"Data Scientist": ["python","pandas","numpy","machine learning","statistics","data visualization","matplotlib"],

"Machine Learning Engineer": ["python","tensorflow","pytorch","machine learning","data","deep learning","model deployment"],

"DevOps Engineer": ["docker","kubernetes","linux","aws","ci/cd","git","monitoring"],

"Cloud Engineer": ["aws","azure","cloud","docker","linux","networking","security"],

"Cybersecurity Analyst": ["network","security","linux","cryptography","penetration testing","vulnerability analysis","firewalls"],

"Game Developer": ["c++","unity","c#","game design","graphics","physics engines","animation"],

"UI Designer": ["figma","design","ui","prototype","adobe","typography","color theory"],

"UX Designer": ["research","wireframe","prototype","figma","usability","user testing","interaction design"],

"Mobile App Developer": ["java","kotlin","android","flutter","dart","firebase","rest api"],

"iOS Developer": ["swift","ios","xcode","ui kit","swiftui","core data","api integration"],

"Android Developer": ["java","kotlin","android studio","xml","firebase","rest api","material design"],

"AI Engineer": ["python","machine learning","deep learning","tensorflow","data","neural networks","nlp"],

"Blockchain Developer": ["solidity","ethereum","smart contracts","web3","cryptography","blockchain","defi"],

"Database Administrator": ["sql","mysql","database","performance tuning","backup","replication","indexing"],

"Software Tester": ["testing","selenium","automation","qa","bug tracking","test cases","performance testing"],

"System Administrator": ["linux","network","server","security","scripting","backup","monitoring"]

};

const roleAverageSalaries = {
    "Frontend Developer": 6,
    "Backend Developer": 7,
    "Full Stack Developer": 8,
    "Software Engineer": 9,
    "Data Scientist": 10,
    "Machine Learning Engineer": 12,
    "AI Engineer": 14,
    "DevOps Engineer": 9,
    "Cybersecurity Analyst": 8,
    "Game Developer": 7,
    "Cloud Engineer": 10,
    "UI Designer": 5,
    "UX Designer": 6,
    "Mobile App Developer": 7,
    "iOS Developer": 8,
    "Android Developer": 7,
    "Blockchain Developer": 11,
    "Database Administrator": 7,
    "Software Tester": 5,
    "System Administrator": 6
};

let resumeExtractedSkills = [];
let lastAnalysis = null;


function normalizeText(s){
    return (s || "")
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[\u2019’]/g, "'")
        .replace(/[^\p{L}\p{N}#+.\s-]+/gu, " ")
        .replace(/\s+/g, " ")
        .trim();
}

function canonicalSkillKey(skill){
    // Canonical key for fuzzy matching: strip separators, normalize "js"/"ts" suffixes.
    let s = normalizeText(skill);
    s = s.replace(/\s+/g, "");
    s = s.replace(/\.js$/,"js").replace(/\.ts$/,"ts");
    s = s.replace(/(javascript|java-script)/g, "javascript");
    s = s.replace(/(nodejs|node\.js)/g, "node");
    s = s.replace(/(reactjs|react\.js)/g, "react");
    s = s.replace(/(nextjs|next\.js)/g, "nextjs");
    s = s.replace(/(vuejs|vue\.js)/g, "vue");
    s = s.replace(/(nuxtjs|nuxt\.js)/g, "nuxt");
    s = s.replace(/(typescript)/g, "typescript");
    s = s.replace(/(postgres|postgre)/g, "postgresql");
    s = s.replace(/(mongo|mongodb)/g, "mongodb");
    s = s.replace(/(tailwindcss)/g, "tailwind");
    s = s.replace(/(csharp|c#)/g, "c#");
    s = s.replace(/(cplusplus|c\+\+)/g, "c++");
    // Normalize common short forms
    if(s === "cpp") s = "c++";
    if(s === "c") return "c";   // keep plain C as its own skill
    return s;
}

function normalizeSkill(skill){
    // Returns the displayed normalized skill (not the key).
    const key = canonicalSkillKey(skill);
    const keyToDisplay = {
        "js":"javascript",
        "javascript":"javascript",
        "react":"react",
        "node":"node",
        "express":"express",
        "mongo":"mongodb",
        "mongodb":"mongodb",
        "postgres":"postgresql",
        "postgresql":"postgresql",
        "tailwindcss":"tailwind",
        "tailwind":"tailwind",
        "csharp":"c#",
        "c#":"c#",
        "cplusplus":"c++",
        "c++":"c++",
        "cpp":"c++",
        "c":"c",
        "ml":"machine learning",
        "machinelearning":"machine learning",
        "deeplearning":"deep learning",
        "ai":"ai",
        "artificialintelligence":"ai",
        "cicd":"ci/cd",
        "restapi":"rest api"
    };
    return keyToDisplay[key] || normalizeText(skill);
}

function dedupeSkills(skillsArr){
    const seen = new Set();
    const out = [];
    for(const s of skillsArr){
        const norm = normalizeSkill(s);
        const k = canonicalSkillKey(norm);
        if(!k) continue;
        if(seen.has(k)) continue;
        seen.add(k);
        out.push(norm);
    }
    return out;
}

/* ---------- Skill Typo Detection ---------- */

// Build a master list of valid skills from roleSkills
const allKnownSkills = Array.from(
    new Set(
        Object.values(roleSkills)
            .flat()
            .map(s => normalizeSkill(s))
    )
);

// Simple Levenshtein distance function
function levenshtein(a, b){
    const matrix = Array.from({length: b.length + 1}, () => []);
    for(let i=0;i<=b.length;i++) matrix[i][0] = i;
    for(let j=0;j<=a.length;j++) matrix[0][j] = j;

    for(let i=1;i<=b.length;i++){
        for(let j=1;j<=a.length;j++){
            if(b.charAt(i-1) === a.charAt(j-1)){
                matrix[i][j] = matrix[i-1][j-1];
            }else{
                matrix[i][j] = Math.min(
                    matrix[i-1][j-1] + 1,
                    matrix[i][j-1] + 1,
                    matrix[i-1][j] + 1
                );
            }
        }
    }
    return matrix[b.length][a.length];
}

// Detect typos in skills and warn user
function validateSkillsInput(){
    const skillsInput = document.getElementById("skills");
    if(!skillsInput) return;

    const skills = skillsInput.value
        .split(",")
        .map(s => s.trim())
        .filter(Boolean);

    for(const skill of skills){
        // Skip typo detection for very short language names like C
        if(skill.length <= 2) continue;

        const norm = normalizeSkill(skill);
        let closest = null;
        let minDist = Infinity;

        for(const valid of allKnownSkills){
            const dist = levenshtein(norm, valid);

            if(dist < minDist){
                minDist = dist;
                closest = valid;
            }
        }

        if(minDist > 0 && minDist <= 2){
            skillsInput.setCustomValidity(`Possible typo: "${skill}". Did you mean "${closest}"?`);
            // Force the browser to immediately display the validation message
            skillsInput.reportValidity();
            return;
        }
    }

    skillsInput.setCustomValidity("");
    // Clear any previous warning immediately
    skillsInput.reportValidity();
}

function skillMatches(requiredSkill, userSkills){
    const reqKey = canonicalSkillKey(requiredSkill);
    for(const us of userSkills){
        const userKey = canonicalSkillKey(us);
        if(!userKey) continue;
        if(userKey === reqKey) return true;
        // very small fuzziness: allow containment for compound phrases
        if(userKey.includes(reqKey) || reqKey.includes(userKey)) return true;
    }
    return false;
}

function iconSvg(name){
    // Minimal inline icons (no external dependency)
    const icons = {
        score:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.732-4-2.268Z"/></svg>`,
        skills:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h10v2H4V6Zm0 5h16v2H4v-2Zm0 5h13v2H4v-2Z"/></svg>`,
        projects:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 3H3v7h7V3Zm11 0h-7v7h7V3ZM10 14H3v7h7v-7Zm11 0h-7v7h7v-7Z"/></svg>`,
        roadmap:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10v2H7V3Zm-2 4h14v2H5V7Zm2 4h10v2H7v-2Zm-2 4h14v2H5v-2Zm2 4h10v2H7v-2Z"/></svg>`,
        ai:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a4 4 0 0 0-4 4v1H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2v1a4 4 0 0 0 8 0v-1h2a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2V6a4 4 0 0 0-4-4Zm-2 5V6a2 2 0 1 1 4 0v1h-4Z"/></svg>`,
        company:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21V3h18v18h-2v-2H5v2H3Zm4-4h2v-2H7v2Zm0-4h2v-2H7v2Zm0-4h2V7H7v2Zm4 8h2v-2h-2v2Zm0-4h2v-2h-2v2Zm0-4h2V7h-2v2Zm4 8h2v-2h-2v2Zm0-4h2v-2h-2v2Zm0-4h2V7h-2v2Z"/></svg>`,
        salary:`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.94s4.18 1.36 4.18 3.85c0 1.89-1.44 2.96-3.12 3.19Z"/></svg>`
    };
    return icons[name] || icons.score;
}

function showView(view){
    const views = ["analyzer","career","resume"];
    for(const v of views){
        const el = document.getElementById(`view-${v}`);
        const nav = document.getElementById(`nav-${v}`);
        if(el) el.hidden = v !== view;
        if(nav) nav.classList.toggle("active", v === view);
    }
    // Keep FAQ visible only on analyzer view
    const faq = document.getElementById("faq");
    if(faq) faq.style.display = view === "analyzer" ? "" : "none";
    // Scroll to top for a cleaner UX
    window.scrollTo({ top: 0, behavior: "smooth" });
}

function toggleFaq(btn){
    const answer = btn?.nextElementSibling;
    if(!answer) return;
    const open = !answer.hidden;
    answer.hidden = open;
    btn.classList.toggle("open", !open);
    const chev = btn.querySelector(".chev");
    if(chev) chev.textContent = open ? "⌄" : "⌃";
}

function getRoleMap(){
    return {
        "Frontend Developer": [
            { title:"Foundation (1–2 weeks)", meta:"Must-have basics", items:["HTML + semantic structure","CSS layout (Flex/Grid)","JavaScript fundamentals","Git basics"] },
            { title:"Core Frontend (2–4 weeks)", meta:"Build real UIs", items:["React basics (components, state, props)","Routing + forms","API integration (fetch)","Responsive design"] },
            { title:"Professional Skills (2–4 weeks)", meta:"Industry-ready", items:["Performance + accessibility","Testing basics","Deployments","Portfolio projects (2+)"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["DSA basics (arrays/strings)","Frontend interview questions","System design (basic)","Mock interviews"] }
        ],
        "Backend Developer": [
            { title:"Foundation (1–2 weeks)", meta:"Backend essentials", items:["HTTP basics","Node or PHP fundamentals","SQL basics","Git basics"] },
            { title:"APIs + Auth (2–4 weeks)", meta:"Build services", items:["REST APIs","Authentication (JWT/session)","Validation + error handling","Database design"] },
            { title:"Production Skills (2–4 weeks)", meta:"Scale safely", items:["Caching basics","Security basics","Testing","Deployment"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["DSA basics","Backend design questions","System design (basic)","Debugging practice"] }
        ],
        "Full Stack Developer": [
            { title:"Foundation (2–3 weeks)", meta:"End-to-end basics", items:["HTML/CSS/JS","React basics","APIs (REST)","SQL + data modeling"] },
            { title:"Full Stack Projects (3–5 weeks)", meta:"Proof of skill", items:["Auth + roles","CRUD dashboard","File upload","Deploy full stack app"] },
            { title:"Engineering Habits (2–4 weeks)", meta:"Professional quality", items:["Testing + linting","CI/CD basics","Performance + security","Documentation"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["DSA","System design","Behavioral (STAR)","Project deep dives"] }
        ],
        "Data Scientist": [
            { title:"Foundation (2–3 weeks)", meta:"Core tools", items:["Python","Numpy/Pandas","Statistics basics","Data visualization"] },
            { title:"Modeling (3–5 weeks)", meta:"ML workflow", items:["Feature engineering","Model training + evaluation","Cross-validation","Experiment tracking basics"] },
            { title:"Projects (2–4 weeks)", meta:"Portfolio-ready", items:["End-to-end DS project","Dashboard/storytelling","Clean notebook/report","GitHub repo polish"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["Probability + stats","ML interview questions","SQL practice","Case studies"] }
        ],
        "Machine Learning Engineer": [
            { title:"Foundation (2–3 weeks)", meta:"Core ML", items:["Python","ML fundamentals","Deep learning intro","Data pipelines basics"] },
            { title:"Production ML (3–6 weeks)", meta:"Deploy models", items:["Model serving","Monitoring + drift","Versioning","Latency + performance"] },
            { title:"Projects (2–4 weeks)", meta:"Proof of deployment", items:["Model API service","Pipeline + training code","Evaluation report","Deployment demo"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["ML + DL theory","System design (ML)","Coding rounds","Behavioral"] }
        ],
        "AI Engineer": [
            { title:"Foundation (2–3 weeks)", meta:"AI fundamentals", items:["Python","ML/DL basics","NLP or CV basics","Data handling"] },
            { title:"Applied AI (3–6 weeks)", meta:"Build AI apps", items:["Prompting + evaluation (if LLM)","Model fine-tuning basics","Vector search basics","Safety + testing"] },
            { title:"Projects (2–4 weeks)", meta:"Portfolio-ready", items:["AI assistant app","RAG-style project","Deployment demo","Metrics + writeup"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["AI concepts","System design","Coding rounds","Project deep dives"] }
        ],
        "DevOps Engineer": [
            { title:"Foundation (2–3 weeks)", meta:"Ops basics", items:["Linux","Networking basics","Git","Scripting basics"] },
            { title:"Containers + Cloud (3–6 weeks)", meta:"Modern infra", items:["Docker","Kubernetes basics","Cloud fundamentals","CI/CD pipelines"] },
            { title:"Reliability (2–4 weeks)", meta:"Operate systems", items:["Monitoring + alerts","Logging","Security basics","Infra as code"] },
            { title:"Interview Prep (ongoing)", meta:"Get hired", items:["Linux troubleshooting","K8s basics","Cloud scenarios","Incident stories"] }
        ]
    };
}

function generateCareerMap(){
    const role = document.getElementById("careerRole")?.value || "";
    const out = document.getElementById("careerMap");
    if(!out){
        return;
    }
    if(!role){
        out.innerHTML = `<p class="helper">Select a role above to see a career map.</p>`;
        return;
    }
    const maps = getRoleMap();
    const steps = maps[role] || [
        { title:"Foundation", meta:"Start here", items:["Role fundamentals","Build 1 small project","Learn Git + deployment"] },
        { title:"Projects", meta:"Prove your skill", items:["Build 2–3 real projects","Add documentation","Deploy + share demos"] },
        { title:"Interview Prep", meta:"Get hired", items:["Practice coding","Review role questions","Mock interviews"] }
    ];
    out.innerHTML = `
      <div class="helper">Role: <b>${role}</b></div>
      <div class="steps">
        ${steps.map((s,i)=>`
          <div class="step">
            <div class="step-title">
              <span>${i+1}. ${s.title}</span>
              <span class="step-meta">${s.meta}</span>
            </div>
            <ul>${s.items.map(it=>`<li>${it}</li>`).join("")}</ul>
          </div>
        `).join("")}
      </div>
    `;
}

function useAnalyzerData(){
    const skills = document.getElementById("skills")?.value || "";
    const projects = document.getElementById("projects")?.value || "";
    const role = document.getElementById("role")?.value || "";
    const years = document.getElementById("experience")?.value || "";
    const months = document.getElementById("experienceMonths")?.value || "";
    const internship = document.getElementById("internship")?.checked;
    const expText = internship ? "Internship" : `${years || 0} years ${months || 0} months`;
    const roleOut = document.getElementById("resumeTargetRole");
    const expOut = document.getElementById("resumeExperience");
    const skillsOut = document.getElementById("resumeSkills");
    const projectsOut = document.getElementById("resumeProjects");
    if(roleOut && role) roleOut.value = role;
    if(expOut) expOut.value = expText.trim();
    if(skillsOut) skillsOut.value = skills;
    if(projectsOut) projectsOut.value = projects;
    const s = document.getElementById("resumeGenStatus");
    if(s) s.textContent = "Copied analyzer fields into the resume writer.";
}

function sentenceCase(s){
    const t = (s || "").trim();
    if(!t) return "";
    return t.charAt(0).toUpperCase() + t.slice(1);
}

function generateResume(){
    const targetRole = document.getElementById("resumeTargetRole")?.value || "";
    const exp = document.getElementById("resumeExperience")?.value || "";
    const skillsRaw = document.getElementById("resumeSkills")?.value || "";
    const projectsRaw = document.getElementById("resumeProjects")?.value || "";
    const textEl = document.getElementById("resumeText");
    const status = document.getElementById("resumeGenStatus");
    if(!textEl) return;

    const skills = dedupeSkills(skillsRaw.split(",")).slice(0, 18);
    const projects = projectsRaw.split(",").map(p=>p.trim()).filter(Boolean).slice(0, 6);

    const roleLine = targetRole ? `Target Role: ${sentenceCase(targetRole)}` : "Target Role: —";
    const expLine = exp ? `Experience: ${exp}` : "Experience: —";

    const summary = [
        `Summary`,
        `- ${targetRole ? "Aspiring " + sentenceCase(targetRole) : "Aspiring software professional"} with ${exp ? exp : "hands-on experience"} building real projects.`,
        `- Strong fundamentals and practical skills in ${skills.length ? skills.slice(0, Math.min(6, skills.length)).join(", ") : "modern development tools"}.`,
        `- Comfortable collaborating with Git and learning fast through project-based work.`
    ].join("\n");

    const skillsSection = [
        `Skills`,
        skills.length ? `- ${skills.join(", ")}` : `- (Add your skills here)`
    ].join("\n");

    const projBullets = (pName) => {
        const base = [
            `- Built ${pName} with a focus on clean UI and solid fundamentals.`,
            `- Implemented core features, handled edge cases, and improved reliability through testing/debugging.`,
            `- Deployed and documented the project for easy review (README + demo link).`
        ];
        if(lastAnalysis?.missing?.length){
            base.push(`- Next iteration: add ${lastAnalysis.missing.slice(0,2).join(" + ")} to strengthen role readiness.`);
        }
        return base;
    };

    const projectsSection = [
        `Projects`,
        projects.length
            ? projects.map(p=>[`• ${p}`, ...projBullets(p)].join("\n")).join("\n\n")
            : `- (Add 2–3 projects here. Example: Chat App, E-commerce Store, Portfolio Website)`
    ].join("\n");

    const out = [
        roleLine,
        expLine,
        ``,
        summary,
        ``,
        skillsSection,
        ``,
        projectsSection
    ].join("\n");

    textEl.value = out;
    if(status) status.textContent = "Generated resume content. Edit it freely, then copy or download.";
}

async function copyResume(){
    const textEl = document.getElementById("resumeText");
    const status = document.getElementById("resumeGenStatus");
    const val = textEl?.value || "";
    if(!val.trim()){
        if(status) status.textContent = "Nothing to copy yet—generate content first.";
        return;
    }
    try{
        await navigator.clipboard.writeText(val);
        if(status) status.textContent = "Copied to clipboard.";
    }catch(e){
        if(status) status.textContent = "Copy failed. Select the text and copy manually.";
    }
}

function downloadResume(){
    const val = document.getElementById("resumeText")?.value || "";
    const status = document.getElementById("resumeGenStatus");
    if(!val.trim()){
        if(status) status.textContent = "Nothing to download yet—generate content first.";
        return;
    }
    const blob = new Blob([val], { type:"text/plain;charset=utf-8" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "resume-content.txt";
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(()=>URL.revokeObjectURL(a.href), 2000);
    if(status) status.textContent = "Downloaded resume-content.txt";
}

// Initialize default view + small accessibility helpers
document.addEventListener("DOMContentLoaded", () => {
    showView("analyzer");
    const brand = document.querySelector(".brand");
    if(brand){
        brand.addEventListener("keydown", (e) => {
            if(e.key === "Enter" || e.key === " "){
                e.preventDefault();
                showView("analyzer");
            }
        });
    }
    const skillsInput = document.getElementById("skills");
    if(skillsInput){
        skillsInput.addEventListener("input", validateSkillsInput);
    }
});









async function extractResumeSkills(){
    const file = document.getElementById("resume")?.files?.[0];
    const status = document.getElementById("resumeStatus");
    if(!file){
        if(status) status.textContent = "Please select a PDF file first.";
        return;
    }
    if(status) status.textContent = "Extracting skills from PDF…";
    try{
        const fd = new FormData();
        fd.append("resume", file);
        const resp = await fetch("uploadResume.php", { method:"POST", body: fd });
        const data = await resp.json();
        if(!resp.ok || !data?.ok){
            throw new Error(data?.error || "Resume extraction failed");
        }
        resumeExtractedSkills = dedupeSkills(data.skills || []);
        const skillsInput = document.getElementById("skills");
        const existing = dedupeSkills((skillsInput?.value || "").split(","));
        const merged = dedupeSkills([...existing, ...resumeExtractedSkills]);
        if(skillsInput) skillsInput.value = merged.join(", ");
        if(status) status.textContent = `Extracted ${resumeExtractedSkills.length} skills and merged into Skills field.`;
    }catch(e){
        resumeExtractedSkills = [];
        if(status) status.textContent = `Could not extract skills. ${e.message}`;
    }
}

function computeProjectComplexityScore(projectTitles){
    const weights = [
        // very simple
        { re:/\b(calculator|counter|todo|to-do|tic tac toe|tictactoe|guess|timer|stopwatch)\b/i, pts:1 },
        // simple/medium
        { re:/\b(portfolio|landing|static|resume|gallery|blog|notes|weather)\b/i, pts:3 },
        { re:/\b(crud|cms|admin|dashboard)\b/i, pts:5 },
        // complex
        { re:/\b(auth|oauth|jwt|rbac|payments?|stripe|razorpay|paypal)\b/i, pts:8 },
        { re:/\b(chat|messaging|realtime|real-time|websocket|socket\.io)\b/i, pts:9 },
        { re:/\b(e-?commerce|store|cart|checkout|orders)\b/i, pts:9 },
        { re:/\b(full\s*stack|microservices?|serverless|kubernetes|docker|ci\/cd)\b/i, pts:10 },
        { re:/\b(ai|ml|machine learning|deep learning|nlp|computer vision|recommendation)\b/i, pts:10 },
        { re:/\b(api|graphql|rest|backend|scalable|system design)\b/i, pts:7 },
        { re:/\b(mobile|android|ios|react native|flutter)\b/i, pts:7 }
    ];

    let total = 0;
    for(const titleRaw of projectTitles){
        const title = (titleRaw || "").toString();
        let projectPts = 2; // base
        for(const w of weights){
            if(w.re.test(title)) projectPts += w.pts;
        }
        // Penalize if explicitly "basic"/"demo"
        if(/\b(basic|demo|practice|beginner)\b/i.test(title)) projectPts = Math.max(1, projectPts - 3);
        projectPts = Math.min(12, projectPts);
        total += projectPts;
    }
    return Math.min(25, total);
}

function readinessBadge(score){
    if(score > 80) return { label:"Job Ready", cls:"good" };
    if(score >= 60) return { label:"Almost Ready", cls:"warn" };
    return { label:"Needs Improvement", cls:"bad" };
}

function prioritizeSkills(role, missingSkills){
    const baseWeights = {
        "html":90, "css":85, "javascript":95, "git":70,
        "react":80, "bootstrap":55, "tailwind":55,
        "node":85, "express":75, "api":80, "mysql":75, "mongodb":70, "authentication":80,
        "python":90, "pandas":75, "numpy":75, "statistics":80, "machine learning":90, "deep learning":85,
        "docker":75, "kubernetes":70, "linux":80, "aws":75, "ci/cd":70, "monitoring":65
    };
    const roleBoost = {
        "Frontend Developer": { "javascript":10, "react":10, "html":5, "css":5 },
        "Backend Developer": { "node":10, "api":10, "authentication":10, "mysql":8 },
        "Full Stack Developer": { "javascript":10, "node":8, "react":8, "api":8, "mysql":6, "git":5 },
        "Data Scientist": { "python":10, "statistics":10, "machine learning":8, "data visualization":6 },
        "Machine Learning Engineer": { "python":10, "machine learning":10, "deep learning":8, "model deployment":8 },
        "AI Engineer": { "python":10, "deep learning":10, "nlp":8, "neural networks":8 },
        "DevOps Engineer": { "linux":10, "docker":10, "kubernetes":8, "aws":8, "ci/cd":8 },
    };
    const boosts = roleBoost[role] || {};

    const scored = missingSkills.map((s, idx) => {
        const ns = normalizeSkill(s);
        const w = (baseWeights[ns] || 50) + (boosts[ns] || 0);
        // keep stable ordering when equal: prefer earlier required skill order
        return { skill: ns, w, idx };
    });
    scored.sort((a,b)=> b.w - a.w || a.idx - b.idx);
    return scored.map(x=>x.skill);
}

function companyPreparation(company, companyRole, role, requiredSkills, missingSkills, experienceYears){
    const c = normalizeText(company);
    const targetRole = normalizeText(companyRole || role);

    const isBigTech = /\b(google|alphabet|microsoft|amazon|meta|facebook|apple|netflix|uber|linkedin)\b/i.test(c);
    const isStartup = /\b(startup|start-up)\b/i.test(c);

    const expBand = experienceYears >= 5 ? "Senior (5+ years)" : (experienceYears >= 2 ? "Mid (2–5 years)" : "Junior (0–2 years)");

    const recommended = new Set(requiredSkills.map(s=>normalizeSkill(s)));
    // generally useful
    ["data structures & algorithms","problem solving","testing","debugging","communication"].forEach(x=>recommended.add(x));
    if(/full stack|backend|software engineer/i.test(targetRole)) recommended.add("system design");
    if(/frontend/i.test(targetRole)) recommended.add("performance optimization");
    if(/devops|cloud/i.test(targetRole)) ["cloud fundamentals","infra as code"].forEach(x=>recommended.add(x));
    if(isBigTech) ["leetcode-style interviews","system design interviews","behavioral interviews (STAR)"].forEach(x=>recommended.add(x));
    if(isStartup) ["ownership mindset","shipping fast","product thinking"].forEach(x=>recommended.add(x));

    const prep = [];
    prep.push(`Target: ${company ? company : "—"} (${companyRole ? companyRole : role})`);
    prep.push(`Typical experience focus: ${expBand}`);
    if(isBigTech) prep.push("Interview focus: DSA + System Design + Behavioral");
    if(!isBigTech) prep.push("Interview focus: Role fundamentals + Projects + Problem solving");
    if(missingSkills.length > 0) prep.push(`Fastest improvement: close your top ${Math.min(5, missingSkills.length)} skill gaps first.`);

    return {
        expBand,
        recommendedSkills: Array.from(recommended),
        suggestions: prep
    };
}

function analyze(){

let name = document.getElementById("name").value.trim()
let email = document.getElementById("email").value.trim()
// Collect selected roles from checkbox dropdown
let roleCheckboxes = document.querySelectorAll('input[name="roles"]:checked');
let selectedRoles = Array.from(roleCheckboxes).map(cb => cb.value.trim());
let role = selectedRoles.join(", ");

// Get selected gender from radio buttons
let genderElement = document.querySelector('input[name="gender"]:checked')
let gender = genderElement ? genderElement.value : ""

let skills = document.getElementById("skills").value.toLowerCase().trim()

let projectInput = document.getElementById("projects").value.toLowerCase().trim()
let projects = 0

let projectTitles = projectInput !== ""
    ? projectInput.split(",").map(p=>p.trim()).filter(Boolean)
    : [];
projects = projectTitles.length;

// Read experience in years and months
let experienceYears = parseInt(document.getElementById("experience").value) || 0
let experienceMonths = parseInt(document.getElementById("experienceMonths")?.value) || 0

// Convert everything to total years (including months)
let totalMonths = (experienceYears * 12) + experienceMonths
let experience = totalMonths / 12
let internship = document.getElementById("internship").checked
let salaryExpectation = parseFloat(document.getElementById("salaryExpectation")?.value) || 0

/* Validate required fields */
if(name === "" || email === "" || skills === ""){
    document.getElementById("result").innerText = "Please enter Name, Email, and Skills.";
    return;
}

// Validation for roles selection
if(selectedRoles.length === 0){
    document.getElementById("result").innerText = "Please select at least one role.";
    return;
}

// Allow skills separated ONLY by commas
let userSkills = dedupeSkills(skills.split(","));
// merge in resume extracted skills (if any)
userSkills = dedupeSkills([...userSkills, ...resumeExtractedSkills]);

// Combine required skills from all selected roles
let requiredSkills = [];
selectedRoles.forEach(r => {
    if(roleSkills[r]){
        requiredSkills = requiredSkills.concat(roleSkills[r]);
    }
});

// Remove duplicate skills
requiredSkills = [...new Set(requiredSkills)];

let matchedSkills = []
let missingSkills = []

requiredSkills.forEach(skill => {
    if(skillMatches(skill, userSkills)){
        matchedSkills.push(skill)
    }else{
        missingSkills.push(skill)
    }
})

// ----- Advanced scoring system -----

// Skill score (0–60 based on percentage match)
let skillScore = Math.round((matchedSkills.length / requiredSkills.length) * 60) || 0

// Project score based on keyword-weighted complexity
let projectScore = computeProjectComplexityScore(projectTitles);

// Experience score (0–10)
let experienceScore = 0
if(experience >= 4){
    experienceScore = 10
}else if(experience >= 2){
    experienceScore = 7
}else if(experience >= 1){
    experienceScore = 5
}

// Internship bonus (0–10)
let internshipScore = internship ? 10 : 0

// Total score out of 100
let score = skillScore + projectScore + experienceScore + internshipScore
if(score > 100) score = 100;

let totalSkills = requiredSkills.length
let percent = totalSkills > 0 ? Math.round((matchedSkills.length / totalSkills) * 100) : 0

const gapMissing = missingSkills.length;
const gapTotal = requiredSkills.length || 0;
const readiness = readinessBadge(score);
const prioritizedMissing = prioritizeSkills(role, missingSkills);
lastAnalysis = { role, matched: matchedSkills, missing: prioritizedMissing, score, percent };

let aiInsightText = "";
if(score > 80){
    aiInsightText = "You’re in strong shape. Focus on depth: advanced projects, performance, testing, and interview prep.";
}else if(score >= 60){
    aiInsightText = "You’re close. Close the top skill gaps, and add 1–2 role-relevant projects to prove those skills.";
}else{
    aiInsightText = "Your foundation needs strengthening. Follow the prioritized roadmap and build smaller projects that showcase each skill.";
}

const company = document.getElementById("company")?.value || "";
const companyRole = document.getElementById("companyRole")?.value || "";
const companyPrep = companyPreparation(company, companyRole, role, requiredSkills, prioritizedMissing, experienceYears);

// Salary calculation: average salary across selected roles
// Average salary across selected roles
let avgSalary = 0;
if(selectedRoles.length){
    let totalSalary = 0;
    let count = 0;
    selectedRoles.forEach(r=>{
        if(roleAverageSalaries[r]){
            totalSalary += roleAverageSalaries[r];
            count++;
        }
    });
    avgSalary = count ? Math.round(totalSalary / count) : 0;
}
let salaryBadge = "";
let salaryInsight = "";
if(salaryExpectation > 0 && avgSalary > 0){
    const diff = salaryExpectation - avgSalary;
    const diffPercent = Math.round((diff / avgSalary) * 100);
    if(diff > 0){
        salaryBadge = `<span class="badge warn">${diffPercent}% above avg</span>`;
        salaryInsight = `Your expectation of ₹${salaryExpectation} LPA is <b>${diffPercent}%</b> above the average of ₹${avgSalary} LPA for ${role}. Focus on building a strong portfolio to justify the higher ask.`;
    }else if(diff < 0){
        salaryBadge = `<span class="badge good">${Math.abs(diffPercent)}% below avg</span>`;
        salaryInsight = `Your expectation of ₹${salaryExpectation} LPA is <b>${Math.abs(diffPercent)}%</b> below the average of ₹${avgSalary} LPA for ${role}. You may be undervaluing yourself—consider aiming closer to the market rate.`;
    }else{
        salaryBadge = `<span class="badge good">At average</span>`;
        salaryInsight = `Your expectation of ₹${salaryExpectation} LPA matches the industry average of ₹${avgSalary} LPA for ${role}. Well calibrated!`;
    }
}else if(avgSalary > 0){
    salaryInsight = `The average salary for ${role} is <b>₹${avgSalary} LPA</b>. Enter your salary expectation above to see how you compare.`;
    salaryBadge = `<span class="badge">No input</span>`;
}

const dashboardEl = document.getElementById("dashboard");
document.getElementById("result").querySelector("p")?.remove?.();

const pillsMatched = matchedSkills.length
    ? matchedSkills.map(s=>`<span class="pill matched">${s}</span>`).join("")
    : `<span class="pill">None yet</span>`;
const pillsMissing = missingSkills.length
    ? prioritizedMissing.map(s=>`<span class="pill missing">${s}</span>`).join("")
    : `<span class="pill matched">No missing skills</span>`;

const roadmapItems = prioritizedMissing.slice(0, 7).map((s,i)=>`<li><b>${i+1}.</b> Learn <b>${s}</b> (start with freeCodeCamp/MDN + a mini-project)</li>`).join("");
const projectsList = projectTitles.length ? projectTitles.map(p=>`<li>${p}</li>`).join("") : `<li>No projects entered yet.</li>`;

dashboardEl.innerHTML = `
  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("score")}</span>Portfolio Score</div>
      <span class="badge ${readiness.cls}">${readiness.label}</span>
    </div>
    <div class="metric">
      <div class="big">${score}/100</div>
      <span class="badge">Skill ${skillScore}</span>
      <span class="badge">Projects ${projectScore}</span>
      <span class="badge">Experience ${experienceScore}</span>
      <span class="badge">Internship ${internshipScore}</span>
    </div>
  </div>

  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("skills")}</span>Skill Match</div>
      <span class="badge">${percent}% match</span>
    </div>
    <div class="progress" aria-label="Skill match progress"><div style="width:${percent}%"></div></div>
    <div style="margin-top:10px;color:#cbd5e1">
      <b>Skill Gap:</b> ${gapMissing} out of ${gapTotal} skills missing
    </div>
    <div style="margin-top:10px">
      <div style="margin-bottom:6px;color:#94a3b8">Matched</div>
      <div class="skills-row">${pillsMatched}</div>
    </div>
    <div style="margin-top:10px">
      <div style="margin-bottom:6px;color:#94a3b8">Missing (prioritized)</div>
      <div class="skills-row">${pillsMissing}</div>
    </div>
  </div>

  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("projects")}</span>Projects</div>
      <span class="badge">${projects} total</span>
    </div>
    <ul class="list">${projectsList}</ul>
  </div>

  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("roadmap")}</span>Learning Roadmap</div>
      <span class="badge">${prioritizedMissing.length ? "Prioritized" : "Complete"}</span>
    </div>
    ${
      prioritizedMissing.length
        ? `<ul class="list">${roadmapItems}</ul>`
        : `<div style="color:#cbd5e1">You already cover the key skills for this role—focus on advanced projects and interview prep.</div>`
    }
  </div>

  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("ai")}</span>AI Insight</div>
    </div>
    <div style="color:#cbd5e1">${aiInsightText}</div>
  </div>

  <div class="dash-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon">${iconSvg("company")}</span>Company Preparation</div>
      <span class="badge">${companyPrep.expBand}</span>
    </div>
    <ul class="list">
      ${companyPrep.suggestions.map(s=>`<li>${s}</li>`).join("")}
    </ul>
    <div style="margin-top:10px;color:#94a3b8">Recommended focus skills</div>
    <div class="skills-row" style="margin-top:8px">
      ${companyPrep.recommendedSkills.slice(0, 12).map(s=>`<span class="pill">${s}</span>`).join("")}
    </div>
  </div>

  <div class="dash-card salary-card">
    <div class="dash-head">
      <div class="dash-title"><span class="dash-icon salary-icon">${iconSvg("salary")}</span>Salary Insight</div>
      ${salaryBadge}
    </div>
    <div class="salary-compare">
      <div class="salary-figures">
        <div class="salary-fig">
          <div class="salary-label">Your Expectation</div>
          <div class="salary-value">₹${salaryExpectation > 0 ? salaryExpectation : '—'} <span>LPA</span></div>
        </div>
        <div class="salary-divider">vs</div>
        <div class="salary-fig">
          <div class="salary-label">Avg for ${role}</div>
          <div class="salary-value">₹${avgSalary} <span>LPA</span></div>
        </div>
      </div>
      <div style="margin-top:12px;color:#cbd5e1">${salaryInsight}</div>
    </div>
  </div>
`;

// Scroll to dashboard after analysis is generated
if(dashboardEl){
    dashboardEl.scrollIntoView({ behavior: "smooth", block: "start" });
}


fetch("saveData.php",{
    method:"POST",
    headers:{
        "Content-Type":"application/json"
    },
    body:JSON.stringify({
        name:name,
        email:email,
        gender:gender,
        role:role,
        company: company,
        company_role: companyRole,
        score:score,
        projects:projects,
        skill_match_percent: percent,
        readiness_label: readiness.label,
        matched_skills: matchedSkills,
        missing_skills: prioritizedMissing,
        analysis_date: new Date().toISOString(),
        experience_years: experienceYears,
        experience_months: experienceMonths,
        experience_total: experience,
        salary_expectation: salaryExpectation
    })
})

}
// Warn user before refreshing or leaving the page if form fields contain data
window.addEventListener("beforeunload", function (e) {

let name = document.getElementById("name")?.value.trim()
let email = document.getElementById("email")?.value.trim()
let skills = document.getElementById("skills")?.value.trim()
let projects = document.getElementById("projects")?.value.trim()
let experience = document.getElementById("experience")?.value.trim()

// If any field has data, trigger warning
if(name || email || skills || projects || experience){

    e.preventDefault()
    e.returnValue = "You have unsaved changes. If you refresh the page, your entered data will be lost."
    return e.returnValue

}

})