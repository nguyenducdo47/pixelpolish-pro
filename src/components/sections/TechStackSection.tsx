import { motion } from "framer-motion";
import { useLanguage } from "@/contexts/LanguageContext";

// Tech logos using simple SVG icons or styled text
const techLogos: Record<string, { icon: string; color: string }> = {
  "PHP 8": { icon: "🐘", color: "#777BB4" },
  "Laravel": { icon: "⬢", color: "#FF2D20" },
  "MySQL": { icon: "🗄️", color: "#4479A1" },
  "Redis": { icon: "◉", color: "#DC382D" },
  "Blade Template": { icon: "✂️", color: "#F05340" },
  "TailwindCSS": { icon: "🌊", color: "#06B6D4" },
  "Vue.js (Basic)": { icon: "💚", color: "#4FC08D" },
  "React.js (Basic)": { icon: "⚛️", color: "#61DAFB" },
  "Git": { icon: "⎇", color: "#F05032" },
  "Docker": { icon: "🐳", color: "#2496ED" },
  "AWS (Basic)": { icon: "☁️", color: "#FF9900" },
};

interface SkillItem {
  name: string;
  description: string;
  level: number;
}

interface SkillCategory {
  title: string;
  items: SkillItem[];
}

const TechStackSection = () => {
  const { t, language } = useLanguage();

  return (
    <section id="skills" className="py-16 sm:py-20 md:py-24 bg-card/50">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            <span className="text-gradient">{t.skills.title}</span>
          </h2>
          <p className="section-subtitle px-2">
            {t.skills.subtitle}
          </p>
        </motion.div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
          {(t.skills.categories as SkillCategory[]).map((category, categoryIndex) => (
            <motion.div
              key={`${language}-${categoryIndex}`}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: categoryIndex * 0.1 }}
              className="p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-card border border-border hover:border-primary/30 transition-all duration-300"
            >
              <h3 className="text-lg sm:text-xl font-semibold text-primary mb-4 sm:mb-6">{category.title}</h3>
              <div className="space-y-4 sm:space-y-5">
                {category.items.map((item, itemIndex) => {
                  const tech = techLogos[item.name] || { icon: "•", color: "#888" };
                  return (
                    <motion.div
                      key={`${language}-${categoryIndex}-${itemIndex}`}
                      initial={{ opacity: 0, x: -10 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.3, delay: (categoryIndex * 0.1) + (itemIndex * 0.05) }}
                      className="group"
                    >
                      <div className="flex items-center gap-3 mb-2">
                        <span 
                          className="text-xl sm:text-2xl"
                          style={{ filter: 'drop-shadow(0 0 4px ' + tech.color + '40)' }}
                        >
                          {tech.icon}
                        </span>
                        <div className="flex-1 min-w-0">
                          <div className="flex items-center justify-between gap-2">
                            <h4 className="font-medium text-foreground text-sm sm:text-base truncate">
                              {item.name}
                            </h4>
                            <span className="text-xs sm:text-sm font-semibold text-primary flex-shrink-0">
                              {item.level}%
                            </span>
                          </div>
                        </div>
                      </div>
                      
                      {/* Progress bar */}
                      <div className="ml-8 sm:ml-10">
                        <div className="h-2 bg-muted rounded-full overflow-hidden">
                          <motion.div
                            className="h-full rounded-full"
                            style={{ 
                              background: `linear-gradient(90deg, ${tech.color}, ${tech.color}99)`,
                              boxShadow: `0 0 10px ${tech.color}60`
                            }}
                            initial={{ width: 0 }}
                            whileInView={{ width: `${item.level}%` }}
                            viewport={{ once: true }}
                            transition={{ duration: 1, delay: (categoryIndex * 0.1) + (itemIndex * 0.1), ease: "easeOut" }}
                          />
                        </div>
                        <p className="text-xs text-muted-foreground mt-1.5 line-clamp-2">
                          {item.description}
                        </p>
                      </div>
                    </motion.div>
                  );
                })}
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TechStackSection;
