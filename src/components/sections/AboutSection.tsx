import { motion } from "framer-motion";
import { User, Zap, BookOpen } from "lucide-react";
import { useLanguage } from "@/contexts/LanguageContext";

const AboutSection = () => {
  const { t, language } = useLanguage();
  
  const icons = [User, Zap, BookOpen];

  return (
    <section id="about" className="py-16 sm:py-20 md:py-24 relative">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            {t.about.title} / <span className="text-gradient">{t.about.titleEn}</span>
          </h2>
          <p className="section-subtitle max-w-3xl mx-auto px-2">
            {t.about.subtitle}
          </p>
        </motion.div>

        <div className="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="order-2 lg:order-1"
          >
            <p className="text-base sm:text-lg text-foreground/90 leading-relaxed mb-4 sm:mb-6">
              {t.about.intro1}
              <span className="text-primary font-medium"> {t.about.laravelHighlight}</span>
              {t.about.intro1End}
            </p>
            <p className="text-base sm:text-lg text-foreground/90 leading-relaxed mb-4 sm:mb-6">
              {t.about.intro2}
            </p>
            <p className="text-sm sm:text-base text-muted-foreground leading-relaxed">
              {t.about.intro3}
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="grid gap-3 sm:gap-4 order-1 lg:order-2"
          >
            {t.about.highlights.map((item, index) => {
              const Icon = icons[index];
              return (
                <motion.div
                  key={`${language}-${index}`}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.4, delay: 0.1 * index }}
                  className="flex gap-3 sm:gap-4 p-4 sm:p-5 rounded-xl bg-card border border-border hover:border-primary/30 transition-colors"
                >
                  <div className="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Icon className="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                  </div>
                  <div className="min-w-0">
                    <h3 className="font-semibold text-foreground mb-0.5 sm:mb-1 text-sm sm:text-base">{item.title}</h3>
                    <p className="text-xs sm:text-sm text-muted-foreground">{item.description}</p>
                  </div>
                </motion.div>
              );
            })}
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;
