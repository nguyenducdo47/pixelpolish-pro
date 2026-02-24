import { useLanguage } from "@/contexts/LanguageContext";
import { Mail, Github, Linkedin, Printer } from "lucide-react";
import { Button } from "@/components/ui/button";
import LanguageToggle from "@/components/LanguageToggle";
import ThemeToggle from "@/components/ThemeToggle";

const Resume = () => {
  const { t } = useLanguage();
  const resume = t.resume as any;

  const handlePrint = () => {
    window.print();
  };

  return (
    <>
      {/* Toolbar - hidden when printing */}
      <div className="print:hidden fixed top-0 left-0 right-0 z-50 bg-background/80 backdrop-blur-md border-b border-border">
        <div className="max-w-[210mm] mx-auto px-4 py-3 flex items-center justify-between">
          <a href="/portfolio" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
            ← Portfolio
          </a>
          <div className="flex items-center gap-2">
            <LanguageToggle />
            <ThemeToggle />
            <Button variant="outline" size="sm" onClick={handlePrint}>
              <Printer className="w-4 h-4 mr-1" />
              Print / PDF
            </Button>
          </div>
        </div>
      </div>

      {/* A4 Resume */}
      <div className="min-h-screen bg-muted/30 print:bg-white pt-16 print:pt-0 pb-8 print:pb-0">
        <div className="w-full max-w-[210mm] mx-auto bg-background print:shadow-none shadow-xl">
          <div className="p-8 sm:p-12 print:p-[15mm] space-y-5 print:space-y-3 text-foreground print:text-black">

            {/* ===== HEADER ===== */}
            <header className="border-b-2 border-primary print:border-black pb-4 print:pb-3">
              <h1 className="text-3xl print:text-2xl font-bold tracking-tight">
                {t.hero.headline2}
              </h1>
              <p className="text-lg print:text-base text-primary print:text-gray-700 font-semibold mt-1">
                {t.hero.badge}
              </p>
              <div className="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-sm text-muted-foreground print:text-gray-600">
                <a href={`mailto:${t.cta.yourEmail}`} className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Mail className="w-3.5 h-3.5" />
                  {t.cta.yourEmail}
                </a>
                <a href={t.cta.socialLinks.gitHub.url} className="flex items-center gap-1 hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer">
                  <Github className="w-3.5 h-3.5" />
                  GitHub
                </a>
                <a href={t.cta.socialLinks.linkedIn.url} className="flex items-center gap-1 hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer">
                  <Linkedin className="w-3.5 h-3.5" />
                  LinkedIn
                </a>
              </div>
            </header>

            {/* ===== PROFESSIONAL SUMMARY ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.summary}
              </h2>
              <p className="text-sm print:text-xs leading-relaxed text-muted-foreground print:text-gray-700">
                {resume.summary}
              </p>
            </section>

            {/* ===== EDUCATION (đặt trước vì Junior < 2 năm kinh nghiệm) ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.education}
              </h2>
              {resume.education.items.map((edu: any, idx: number) => (
                <div key={idx}>
                  <div className="flex justify-between items-baseline">
                    <h3 className="text-sm font-semibold">{edu.degree}</h3>
                    <span className="text-xs text-muted-foreground print:text-gray-500">{edu.period}</span>
                  </div>
                  <p className="text-xs text-muted-foreground print:text-gray-600">{edu.school}</p>
                  <p className="text-xs text-muted-foreground print:text-gray-600 mt-0.5">{edu.details}</p>
                </div>
              ))}
            </section>

            {/* ===== TECHNICAL SKILLS (keyword list for ATS) ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.skills}
              </h2>
              <div className="space-y-1">
                {Object.entries(resume.skillKeywords as Record<string, string>).map(([category, keywords]) => (
                  <div key={category} className="flex text-sm print:text-xs">
                    <span className="font-semibold w-28 print:w-24 shrink-0">{category}:</span>
                    <span className="text-muted-foreground print:text-gray-700">{keywords}</span>
                  </div>
                ))}
              </div>
            </section>

            {/* ===== WORK EXPERIENCE ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-3">
                {resume.sections.experience}
              </h2>
              <div className="mb-2">
                <div className="flex justify-between items-baseline">
                  <h3 className="text-sm font-bold">{resume.experience.position}</h3>
                  <span className="text-xs text-muted-foreground print:text-gray-500">{resume.experience.period}</span>
                </div>
                <p className="text-xs text-muted-foreground print:text-gray-600 italic">{resume.experience.company}</p>
              </div>
              <div className="space-y-4 print:space-y-3">
                {resume.experience.projects.map((project: any, idx: number) => (
                  <div key={idx}>
                    <div className="flex flex-wrap items-baseline justify-between gap-1">
                      <h4 className="text-sm font-semibold">{project.name}</h4>
                      <span className="text-[10px] text-muted-foreground print:text-gray-500">{project.period}</span>
                    </div>
                    <p className="text-xs text-muted-foreground print:text-gray-600 mt-0.5">
                      {project.description}
                    </p>
                    <ul className="mt-1 space-y-0.5">
                      {project.achievements.map((achievement: string, i: number) => (
                        <li key={i} className="text-xs text-muted-foreground print:text-gray-700 flex items-start gap-1.5">
                          <span className="mt-0.5 shrink-0">•</span>
                          {achievement}
                        </li>
                      ))}
                    </ul>
                    <div className="flex flex-wrap gap-1 mt-1">
                      {project.techStack.map((tech: string, i: number) => (
                        <span
                          key={i}
                          className="px-1.5 py-0.5 rounded text-[10px] bg-secondary print:bg-gray-100 text-secondary-foreground print:text-gray-700 font-medium border border-border print:border-gray-300"
                        >
                          {tech}
                        </span>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {/* ===== LANGUAGES ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.languages}
              </h2>
              <div className="flex flex-wrap gap-x-6 gap-y-1">
                {resume.languages.map((lang: any, idx: number) => (
                  <div key={idx} className="text-sm print:text-xs">
                    <span className="font-semibold">{lang.name}</span>
                    <span className="text-muted-foreground print:text-gray-600"> — {lang.level}</span>
                  </div>
                ))}
              </div>
            </section>

            {/* ===== CORE STRENGTHS ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.strengths}
              </h2>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-1 print:gap-0.5">
                {t.about.highlights.map((highlight, idx) => (
                  <div key={idx} className="text-sm print:text-xs">
                    <span className="font-semibold">{highlight.title}</span>
                    <span className="text-muted-foreground print:text-gray-600"> — {highlight.description}</span>
                  </div>
                ))}
              </div>
            </section>

          </div>
        </div>
      </div>
    </>
  );
};

export default Resume;
