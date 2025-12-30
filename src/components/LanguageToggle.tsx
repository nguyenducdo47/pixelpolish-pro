import { useLanguage } from "@/contexts/LanguageContext";

const LanguageToggle = () => {
  const { language, setLanguage } = useLanguage();

  return (
    <button
      onClick={() => setLanguage(language === "vi" ? "en" : "vi")}
      className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-secondary border border-border hover:border-primary/50 transition-all duration-300 text-xs sm:text-sm font-medium"
      aria-label="Toggle language"
    >
      <span className={language === "vi" ? "text-primary" : "text-muted-foreground"}>VI</span>
      <span className="text-muted-foreground">/</span>
      <span className={language === "en" ? "text-primary" : "text-muted-foreground"}>EN</span>
    </button>
  );
};

export default LanguageToggle;
