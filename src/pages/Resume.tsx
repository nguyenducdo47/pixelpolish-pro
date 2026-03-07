import { useMemo } from "react";
import { Helmet } from "react-helmet-async";
import { useLanguage } from "@/contexts/LanguageContext";
import { Mail, Github, Phone, Printer, ExternalLink, Users, Heart, Smile, Calendar } from "lucide-react";
import { Button } from "@/components/ui/button";
import LanguageToggle from "@/components/LanguageToggle";
import ThemeToggle from "@/components/ThemeToggle";

const Resume = () => {
  const { t, language } = useLanguage();
  const resume = t.resume as any;

  // Auto-generate skill keywords from portfolio skills categories
  const generatedSkillKeywords = useMemo(() => {
    const categories = t.skills.categories as any[];
    const categoryMap: Record<string, string> = {};
    categories.forEach((cat: any) => {
      categoryMap[cat.title] = cat.items.map((item: any) => item.name).join(", ");
    });
    const base = resume.skillKeywords as Record<string, string>;
    const merged: Record<string, string> = {};
    for (const [key, value] of Object.entries(base)) {
      const match = categories.find((c: any) => c.title === key);
      if (match) {
        merged[key] = match.items.map((item: any) => item.name).join(", ");
      } else {
        merged[key] = value as string;
      }
    }
    return merged;
  }, [t, resume]);

  const handlePrint = () => {
    window.print();
  };

  return (
    <>
      <Helmet>
        <html lang={language} />
        <title>Nguyễn Đức Độ | Resume – Web Developer Laravel</title>
        <meta name="description" content="CV / Resume của Nguyễn Đức Độ – Backend Developer PHP Laravel. Kinh nghiệm xây dựng RESTful API, GraphQL, hệ thống quản lý và thương mại điện tử." />
        <link rel="canonical" href="https://pixelpolish-pro.lovable.app/resume" />
        <meta property="og:title" content="Nguyễn Đức Độ | Resume – Web Developer Laravel" />
        <meta property="og:description" content="CV / Resume của Nguyễn Đức Độ – Backend Developer PHP Laravel." />
        <meta property="og:url" content="https://pixelpolish-pro.lovable.app/resume" />
      </Helmet>
      {/* Toolbar - hidden when printing */}
      <div className="print:hidden fixed top-0 left-0 right-0 z-50 bg-background/80 backdrop-blur-md border-b border-border">
        <div className="max-w-[210mm] mx-auto px-4 py-3 flex items-center justify-between">
          <a href="/portfolio" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
            ← Portfolio
          </a>
          <div className="flex items-center gap-2">
            <LanguageToggle />
            <ThemeToggle />
            <Button variant="outline" size="sm" onClick={handlePrint} className="hidden sm:inline-flex">
              <Printer className="w-4 h-4 mr-1" />
              Print / PDF
            </Button>
            <Button variant="outline" size="icon" onClick={handlePrint} className="sm:hidden h-8 w-8">
              <Printer className="w-4 h-4" />
            </Button>
          </div>
        </div>
      </div>

      {/* A4 Resume */}
      <div className="min-h-screen bg-muted/30 print:bg-white pt-16 print:pt-0 pb-8 print:pb-0">
        <div className="w-full max-w-[210mm] mx-auto bg-background print:shadow-none shadow-xl">
          <div className="p-4 sm:p-8 md:p-12 print:p-[15mm] space-y-5 sm:space-y-7 print:space-y-4 text-foreground print:text-black">

            {/* ===== HEADER ===== */}
            <header className="border-b-2 border-primary print:border-black pb-4 print:pb-3 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-0">
              <div>
                <h1 className="text-2xl sm:text-3xl print:text-2xl font-bold tracking-tight">
                  {t.hero.headline2}
                </h1>
                <p className="text-base sm:text-lg print:text-base text-primary print:text-gray-700 font-semibold mt-1">
                  {t.hero.badge} ({resume.positionPeriod})
                </p>
              </div>
              <div className="flex flex-wrap sm:flex-col gap-x-4 gap-y-1 text-xs sm:text-sm text-muted-foreground print:text-gray-600">
                <span className="flex items-center gap-1">
                  <Calendar className="w-3.5 h-3.5 shrink-0" />
                  {resume.dob}
                </span>
                <a href={`mailto:${t.cta.yourEmail}`} target="_blank" rel="noopener noreferrer" className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Mail className="w-3.5 h-3.5 shrink-0" />
                  <span className="truncate">{t.cta.yourEmail}</span>
                </a>
                <a href="tel:0899068281" className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Phone className="w-3.5 h-3.5 shrink-0" />
                  0899068281
                </a>
                <a href={t.cta.socialLinks.gitHub.url} className="flex items-center gap-1 hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer">
                  <Github className="w-3.5 h-3.5 shrink-0" />
                  <span className="truncate">github.com/nguyenducdo47</span>
                </a>
              </div>
            </header>

            {/* ===== PROFESSIONAL SUMMARY ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.summary}
              </h2>
              <div className="text-sm print:text-xs leading-loose text-muted-foreground print:text-gray-700 space-y-2">
                {(resume.summary as string).split('\n\n').map((paragraph: string, idx: number) => (
                  <p key={idx}>{paragraph}</p>
                ))}
              </div>
            </section>

            {/* ===== EDUCATION ===== */}
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

            {/* ===== TECHNICAL SKILLS ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.skills}
              </h2>
              <div className="space-y-1.5">
                {Object.entries(generatedSkillKeywords).map(([category, keywords]) => (
                  <div key={category} className="flex flex-col sm:flex-row text-sm print:text-xs">
                    <span className="font-semibold sm:w-52 print:w-44 shrink-0">{category}:</span>
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
              
              <div className="space-y-5 print:space-y-4">
                {resume.experience.projects.map((project: any, idx: number) => (
                  <div key={idx}>
                    <div className="flex flex-wrap items-baseline justify-between gap-1">
                      <h4 className="text-[13px] font-semibold">{project.name}</h4>
                      <span className="text-[10px] text-muted-foreground print:text-gray-500">{project.period}</span>
                    </div>
                    <div className="flex flex-wrap items-center gap-3 mt-0.5 text-[11px] text-muted-foreground print:text-gray-500">
                      {project.teamSize && (
                        <span className="flex items-center gap-1">
                          <Users className="w-3 h-3" />
                          {project.teamSize}
                        </span>
                      )}
                      {project.demoUrl && (
                        <a
                          href={project.demoUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="flex items-center gap-1 text-primary hover:underline print:text-blue-600"
                        >
                          <ExternalLink className="w-3 h-3" />
                          {project.demoLabel || project.demoUrl}
                        </a>
                      )}
                    </div>
                    <p className="text-xs text-muted-foreground print:text-gray-600 mt-1">
                      {project.description}
                    </p>
                    {project.modules && (
                      <p className="text-xs text-muted-foreground print:text-gray-600 mt-0.5 italic">
                        {project.modules}
                      </p>
                    )}
                    <ul className="mt-1.5 space-y-1">
                      {project.achievements.map((achievement: string, i: number) => (
                        <li key={i} className="text-xs text-muted-foreground print:text-gray-700 flex items-start gap-1.5">
                          <span className="mt-0.5 shrink-0">•</span>
                          {achievement}
                        </li>
                      ))}
                    </ul>
                    <div className="flex flex-wrap gap-1 mt-1.5">
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

            {/* ===== HOBBIES & PERSONALITY ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.hobbies}
              </h2>
              <div className="space-y-1.5">
                <div className="flex items-center gap-2 text-sm print:text-xs">
                  <Heart className="w-3.5 h-3.5 text-primary print:text-gray-600" />
                  <span className="text-muted-foreground print:text-gray-700">{resume.hobbies.sports}</span>
                </div>
                <div className="flex items-center gap-2 text-sm print:text-xs">
                  <Smile className="w-3.5 h-3.5 text-primary print:text-gray-600" />
                  <span className="text-muted-foreground print:text-gray-700">{resume.hobbies.personality}</span>
                </div>
              </div>
            </section>

            {/* ===== CORE STRENGTHS ===== */}
            <section>
              <h2 className="text-base print:text-sm font-bold uppercase tracking-widest border-b border-border print:border-gray-300 pb-1 mb-2">
                {resume.sections.strengths}
              </h2>
              <div className="space-y-1">
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