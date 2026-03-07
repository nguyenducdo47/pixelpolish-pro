import { motion } from "framer-motion";
import { useLanguage } from "@/contexts/LanguageContext";

// Import tech logos
import phpLogo from "@/assets/tech-logos/php.svg";
import laravelLogo from "@/assets/tech-logos/laravel.svg";
import mysqlLogo from "@/assets/tech-logos/mysql.svg";
import mariadbLogo from "@/assets/tech-logos/mariadb.svg";
import mongodbLogo from "@/assets/tech-logos/mongodb.svg";
import redisLogo from "@/assets/tech-logos/redis.svg";
import tailwindLogo from "@/assets/tech-logos/tailwindcss.svg";
import vueLogo from "@/assets/tech-logos/vuejs.svg";
import reactLogo from "@/assets/tech-logos/react.svg";
import gitLogo from "@/assets/tech-logos/git.svg";
import dockerLogo from "@/assets/tech-logos/docker.svg";
import awsLogo from "@/assets/tech-logos/aws.svg";
import bootstrapLogo from "@/assets/tech-logos/bootstrap.svg";
import javascriptLogo from "@/assets/tech-logos/javascript.svg";
import html5Logo from "@/assets/tech-logos/html5.svg";
import css3Logo from "@/assets/tech-logos/css3.svg";
import npmLogo from "@/assets/tech-logos/npm.svg";
import composerLogo from "@/assets/tech-logos/composer.svg";
import postmanLogo from "@/assets/tech-logos/postman.svg";
import vscodeLogo from "@/assets/tech-logos/vscode.svg";
import linuxLogo from "@/assets/tech-logos/linux.svg";
import nginxLogo from "@/assets/tech-logos/nginx.svg";
import typescriptLogo from "@/assets/tech-logos/typescript.svg";
import graphqlLogo from "@/assets/tech-logos/graphql.svg";

// Tech logos mapping
const techLogos: Record<string, { logo: string; color: string }> = {
  PHP: { logo: phpLogo, color: "#777BB4" },
  Laravel: { logo: laravelLogo, color: "#FF2D20" },
  TypeScript: { logo: typescriptLogo, color: "#3178C6" },
  GraphQL: { logo: graphqlLogo, color: "#E10098" },
  MySQL: { logo: mysqlLogo, color: "#4479A1" },
  MariaDB: { logo: mariadbLogo, color: "#003545" },
  MongoDB: { logo: mongodbLogo, color: "#47A248" },
  Redis: { logo: redisLogo, color: "#DC382D" },
  "Blade Template": { logo: laravelLogo, color: "#FF2D20" },
  TailwindCSS: { logo: tailwindLogo, color: "#06B6D4" },
  "Vue.js (Basic)": { logo: vueLogo, color: "#4FC08D" },
  "React.js (Basic)": { logo: reactLogo, color: "#61DAFB" },
  Git: { logo: gitLogo, color: "#F05032" },
  Docker: { logo: dockerLogo, color: "#2496ED" },
  "AWS (Basic)": { logo: awsLogo, color: "#FF9900" },
  BootstrapCSS: { logo: bootstrapLogo, color: "#7952B3" },
  JavaScript: { logo: javascriptLogo, color: "#F7DF1E" },
  HTML5: { logo: html5Logo, color: "#E34F26" },
  CSS3: { logo: css3Logo, color: "#1572B6" },
  NPM: { logo: npmLogo, color: "#CB3837" },
  Composer: { logo: composerLogo, color: "#885630" },
  Postman: { logo: postmanLogo, color: "#FF6C37" },
  "VS Code": { logo: vscodeLogo, color: "#007ACC" },
  Linux: { logo: linuxLogo, color: "#FCC624" },
  Nginx: { logo: nginxLogo, color: "#009639" },
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
          <p className="section-subtitle px-2">{t.skills.subtitle}</p>
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
                  const tech = techLogos[item.name] || { logo: "", color: "#888" };
                  return (
                    <motion.div
                      key={`${language}-${categoryIndex}-${itemIndex}`}
                      initial={{ opacity: 0, x: -10 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.3, delay: categoryIndex * 0.1 + itemIndex * 0.05 }}
                      className="group"
                    >
                      <div className="flex items-center gap-3 mb-2">
                        <div
                          className="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg bg-background/80 p-1.5"
                          style={{ boxShadow: `0 0 12px ${tech.color}30` }}
                        >
                          {tech.logo ? (
                            <img src={tech.logo} alt={item.name} className="w-full h-full object-contain" />
                          ) : (
                            <div
                              className="w-full h-full rounded bg-muted flex items-center justify-center text-xs font-bold"
                              style={{ color: tech.color }}
                            >
                              {item.name.charAt(0)}
                            </div>
                          )}
                        </div>
                        <div className="flex-1 min-w-0">
                          <div className="flex items-center justify-between gap-2">
                            <h4 className="font-medium text-foreground text-sm sm:text-base truncate">{item.name}</h4>
                            <span className="text-xs sm:text-sm font-semibold text-primary flex-shrink-0">
                              {item.level}%
                            </span>
                          </div>
                        </div>
                      </div>

                      {/* Progress bar */}
                      <div className="ml-11 sm:ml-13">
                        <div className="h-2 bg-muted rounded-full overflow-hidden">
                          <motion.div
                            className="h-full rounded-full"
                            style={{
                              background: `linear-gradient(90deg, ${tech.color}, ${tech.color}99)`,
                              boxShadow: `0 0 10px ${tech.color}60`,
                            }}
                            initial={{ width: 0 }}
                            whileInView={{ width: `${item.level}%` }}
                            viewport={{ once: true }}
                            transition={{ duration: 1, delay: categoryIndex * 0.1 + itemIndex * 0.1, ease: "easeOut" }}
                          />
                        </div>
                        <p className="text-xs text-muted-foreground mt-1.5 line-clamp-2">{item.description}</p>
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
