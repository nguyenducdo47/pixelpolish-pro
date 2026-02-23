import { useLanguage } from "@/contexts/LanguageContext";
import { Mail, Github, Linkedin, Facebook, Send, Download, Printer } from "lucide-react";
import { Button } from "@/components/ui/button";
import LanguageToggle from "@/components/LanguageToggle";
import ThemeToggle from "@/components/ThemeToggle";

const Resume = () => {
  const { t } = useLanguage();

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
              Print
            </Button>
          </div>
        </div>
      </div>

      {/* A4 Resume */}
      <div className="min-h-screen bg-muted/30 print:bg-white pt-16 print:pt-0 pb-8 print:pb-0">
        <div className="w-full max-w-[210mm] mx-auto bg-background print:shadow-none shadow-xl">
          <div className="p-8 sm:p-12 print:p-[15mm] space-y-6 print:space-y-4 text-foreground print:text-black">

            {/* Header */}
            <header className="border-b-2 border-primary print:border-black pb-4 print:pb-3">
              <h1 className="text-3xl print:text-2xl font-bold tracking-tight">
                {t.hero.headline2}
              </h1>
              <p className="text-lg print:text-base text-primary print:text-gray-700 font-medium mt-1">
                {t.hero.badge}
              </p>
              <div className="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-sm text-muted-foreground print:text-gray-600">
                <a href={`mailto:${t.cta.yourEmail}`} className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Mail className="w-3.5 h-3.5" />
                  {t.cta.yourEmail}
                </a>
                <a href={t.cta.socialLinks.gitHub.url} className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Github className="w-3.5 h-3.5" />
                  GitHub
                </a>
                <a href={t.cta.socialLinks.linkedIn.url} className="flex items-center gap-1 hover:text-foreground transition-colors">
                  <Linkedin className="w-3.5 h-3.5" />
                  LinkedIn
                </a>
              </div>
            </header>

            {/* Summary */}
            <section>
              <h2 className="text-lg print:text-base font-bold uppercase tracking-wider border-b border-border print:border-gray-300 pb-1 mb-2">
                {t.about.title}
              </h2>
              <p className="text-sm print:text-xs leading-relaxed text-muted-foreground print:text-gray-700">
                {t.hero.description}
              </p>
            </section>

            {/* Skills */}
            <section>
              <h2 className="text-lg print:text-base font-bold uppercase tracking-wider border-b border-border print:border-gray-300 pb-1 mb-3">
                {t.skills.title}
              </h2>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 print:gap-2">
                {t.skills.categories.map((category, idx) => (
                  <div key={idx}>
                    <h3 className="text-sm font-semibold mb-1">{category.title}</h3>
                    <div className="flex flex-wrap gap-1.5">
                      {category.items.map((item, i) => (
                        <span
                          key={i}
                          className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-secondary print:bg-gray-100 text-secondary-foreground print:text-gray-800 border border-border print:border-gray-300"
                        >
                          {item.name}
                          <span className="text-muted-foreground print:text-gray-500">
                            ({item.level}%)
                          </span>
                        </span>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {/* Projects */}
            <section>
              <h2 className="text-lg print:text-base font-bold uppercase tracking-wider border-b border-border print:border-gray-300 pb-1 mb-3">
                {t.projects.title}
              </h2>
              <div className="space-y-4 print:space-y-3">
                {t.projects.items.map((project, idx) => (
                  <div key={idx}>
                    <div className="flex flex-wrap items-baseline justify-between gap-2">
                      <h3 className="text-sm font-semibold">{project.title}</h3>
                      <span className="text-xs text-muted-foreground print:text-gray-500 italic">
                        {project.complexity}
                      </span>
                    </div>
                    <p className="text-xs text-muted-foreground print:text-gray-600 mt-0.5 mb-1">
                      {project.solution}
                    </p>
                    <div className="flex flex-wrap gap-1">
                      {project.techStack.map((tech, i) => (
                        <span
                          key={i}
                          className="px-1.5 py-0.5 rounded text-[10px] bg-primary/10 print:bg-gray-200 text-primary print:text-gray-800 font-medium"
                        >
                          {tech}
                        </span>
                      ))}
                    </div>
                    <ul className="mt-1 space-y-0.5">
                      {project.responsibilities.map((resp, i) => (
                        <li key={i} className="text-xs text-muted-foreground print:text-gray-700 flex items-start gap-1.5">
                          <span className="text-primary print:text-gray-800 mt-0.5">•</span>
                          {resp}
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>
            </section>

            {/* Highlights / Strengths */}
            <section>
              <h2 className="text-lg print:text-base font-bold uppercase tracking-wider border-b border-border print:border-gray-300 pb-1 mb-2">
                Highlights
              </h2>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-2 print:gap-1">
                {t.about.highlights.map((highlight, idx) => (
                  <div key={idx} className="text-sm">
                    <span className="font-semibold">{highlight.title}</span>
                    <span className="text-muted-foreground print:text-gray-600 text-xs ml-1">
                      — {highlight.description}
                    </span>
                  </div>
                ))}
              </div>
            </section>

            {/* Philosophy */}
            <section>
              <h2 className="text-lg print:text-base font-bold uppercase tracking-wider border-b border-border print:border-gray-300 pb-1 mb-2">
                {t.philosophy.title}
              </h2>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 print:gap-1">
                {t.philosophy.principles.map((principle, idx) => (
                  <div key={idx} className="text-xs">
                    <span className="font-semibold text-sm">{principle.title}</span>
                    <p className="text-muted-foreground print:text-gray-600 mt-0.5">{principle.description}</p>
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
