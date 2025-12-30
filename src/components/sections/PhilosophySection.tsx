import { motion } from "framer-motion";
import { Code, Users, TestTube, Lightbulb } from "lucide-react";
import { useLanguage } from "@/contexts/LanguageContext";

const PhilosophySection = () => {
  const { t, language } = useLanguage();
  const icons = [Code, Lightbulb, Users, TestTube];

  // Parse quote with highlight
  const renderQuote = (quote: string) => {
    const parts = quote.split(/<highlight>|<\/highlight>/);
    return parts.map((part, i) => 
      i % 2 === 1 ? (
        <span key={i} className="text-primary font-medium">{part}</span>
      ) : (
        <span key={i}>{part}</span>
      )
    );
  };

  return (
    <section id="philosophy" className="py-16 sm:py-20 md:py-24 bg-card/50">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-10 sm:mb-16"
        >
          <h2 className="section-title">
            <span className="text-gradient">{t.philosophy.title}</span>
          </h2>
          <p className="section-subtitle max-w-2xl mx-auto px-2">
            {t.philosophy.subtitle}
          </p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl mx-auto mb-8 sm:mb-12 p-4 sm:p-6 rounded-xl sm:rounded-2xl bg-card border border-border"
        >
          <p className="text-base sm:text-lg text-foreground/90 leading-relaxed text-center">
            "{renderQuote(t.philosophy.quote)}"
          </p>
        </motion.div>

        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
          {t.philosophy.principles.map((principle, index) => {
            const Icon = icons[index];
            return (
              <motion.div
                key={`${language}-${index}`}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                className="p-3 sm:p-4 md:p-6 rounded-lg sm:rounded-xl bg-card border border-border hover:border-primary/30 transition-all duration-300 text-center group"
              >
                <div className="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mx-auto mb-2 sm:mb-3 md:mb-4 group-hover:bg-primary/20 transition-colors">
                  <Icon className="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 text-primary" />
                </div>
                <h3 className="font-semibold text-foreground mb-1 sm:mb-2 text-xs sm:text-sm md:text-base">{principle.title}</h3>
                <p className="text-[10px] sm:text-xs md:text-sm text-muted-foreground leading-relaxed">{principle.description}</p>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default PhilosophySection;
