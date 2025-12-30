import { useLanguage } from "@/contexts/LanguageContext";
import { motion } from "framer-motion";

const LanguageToggle = () => {
  const { language, setLanguage } = useLanguage();

  return (
    <button
      onClick={() => setLanguage(language === "vi" ? "en" : "vi")}
      className="relative flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-secondary border border-border hover:border-primary/50 transition-all duration-300 text-xs sm:text-sm font-medium overflow-hidden"
      aria-label="Toggle language"
    >
      <motion.span
        animate={{ 
          color: language === "vi" ? "hsl(var(--primary))" : "hsl(var(--muted-foreground))",
          scale: language === "vi" ? 1.05 : 1
        }}
        transition={{ duration: 0.2 }}
      >
        VI
      </motion.span>
      <span className="text-muted-foreground">/</span>
      <motion.span
        animate={{ 
          color: language === "en" ? "hsl(var(--primary))" : "hsl(var(--muted-foreground))",
          scale: language === "en" ? 1.05 : 1
        }}
        transition={{ duration: 0.2 }}
      >
        EN
      </motion.span>
    </button>
  );
};

export default LanguageToggle;
