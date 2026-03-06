-- Portfolio Analyzer Database Schema

-- Developers table (candidate accounts)
CREATE TABLE IF NOT EXISTS developers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recruiters table (company accounts)
CREATE TABLE IF NOT EXISTS recruiters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150),
    recruiter_name VARCHAR(120),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Analyses table (portfolio analysis results)
CREATE TABLE IF NOT EXISTS analyses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    developer_id INT NOT NULL,

    role VARCHAR(150),
    company VARCHAR(150),
    company_role VARCHAR(150),

    score INT,
    projects INT,
    skill_match_percent INT,
    readiness_label VARCHAR(100),

    matched_skills TEXT,
    missing_skills TEXT,

    experience_years INT,
    experience_months INT,
    experience_total FLOAT,

    salary_expectation FLOAT,

    analysis_date DATE,

    FOREIGN KEY (developer_id) REFERENCES developers(id)
);
