import { motion } from "framer-motion";
import { Layers, Shield, ShoppingCart, CreditCard } from "lucide-react";
import Card3D from "@/components/Card3D";
import { useLanguage } from "@/contexts/LanguageContext";

const ProjectsSection = () => {
  const { t, language } = useLanguage();
  const icons = [Layers, Shield, ShoppingCart, CreditCard];

  return (
    <section id="projects" className="py-16 sm:py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            <span className="text-gradient">{t.projects.title}</span>
          </h2>
          <p className="section-subtitle px-2">
            {t.projects.subtitle}
          </p>
        </motion.div>

        <div className="space-y-4 sm:space-y-6 md:space-y-8">
          {t.projects.items.map((project, index) => {
            const Icon = icons[index];
            return (
              <motion.div
                key={`${language}-${index}`}
                initial={{ opacity: 0, y: 40 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
              >
                <Card3D className="cursor-pointer">
                  <article className="group relative p-4 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl bg-card/80 backdrop-blur-sm border border-border/50 hover:border-primary/50 hover:bg-card transition-all duration-500 overflow-hidden">
                    {/* Mobile: Stack layout */}
                    <div className="flex flex-col gap-4 sm:gap-6">
                      {/* Header */}
                      <div>
                        <div className="flex items-start gap-3 sm:gap-4 mb-3 sm:mb-4">
                          <div 
                            className="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/20 transition-colors"
                            style={{ transform: "translateZ(40px)" }}
                          >
                            <Icon className="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                          </div>
                          <div className="min-w-0 flex-1">
                            <h3 
                              className="text-base sm:text-lg md:text-xl font-semibold text-foreground leading-tight group-hover:text-primary transition-colors"
                              style={{ transform: "translateZ(30px)" }}
                            >
                              {project.title}
                            </h3>
                            <p className="text-xs sm:text-sm text-muted-foreground mt-0.5">{project.subtitle}</p>
                          </div>
                        </div>
                        
                        <div className="flex flex-wrap items-center gap-2 mb-3 sm:mb-4">
                          <span 
                            className="inline-block px-2 sm:px-3 py-0.5 sm:py-1 rounded-full bg-secondary text-[10px] sm:text-xs font-medium text-secondary-foreground"
                            style={{ transform: "translateZ(20px)" }}
                          >
                            {project.complexity}
                          </span>
                        </div>
                        
                        {/* Tech stack */}
                        <div className="flex flex-wrap gap-1.5 sm:gap-2" style={{ transform: "translateZ(15px)" }}>
                          {project.techStack.map((tech) => (
                            <span
                              key={tech}
                              className="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md bg-muted text-[10px] sm:text-xs text-muted-foreground group-hover:bg-primary/10 group-hover:text-primary transition-colors"
                            >
                              {tech}
                            </span>
                          ))}
                        </div>
                      </div>

                      {/* Details */}
                      <div className="space-y-3 sm:space-y-4" style={{ transform: "translateZ(10px)" }}>
                        <div>
                          <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">{t.projects.problemLabel}</h4>
                          <p className="text-xs sm:text-sm text-foreground/80 leading-relaxed">{project.problem}</p>
                        </div>
                        
                        <div>
                          <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">{t.projects.solutionLabel}</h4>
                          <p className="text-xs sm:text-sm text-foreground/80 leading-relaxed">{project.solution}</p>
                        </div>

                        <div>
                          <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1.5 sm:mb-2">{t.projects.responsibilitiesLabel}</h4>
                          <ul className="space-y-1 sm:space-y-1.5">
                            {project.responsibilities.map((item, i) => (
                              <li key={i} className="flex items-start gap-2 text-xs sm:text-sm text-foreground/80">
                                <span className="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-primary mt-1.5 sm:mt-2 flex-shrink-0" />
                                <span>{item}</span>
                              </li>
                            ))}
                          </ul>
                        </div>

                        <div className="pt-2 sm:pt-3 border-t border-border">
                          <h4 className="text-xs sm:text-sm font-semibold text-primary mb-1">{t.projects.learnedLabel}</h4>
                          <p className="text-xs sm:text-sm text-muted-foreground italic leading-relaxed">{project.learned}</p>
                        </div>
                      </div>
                    </div>
                  </article>
                </Card3D>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default ProjectsSection;
