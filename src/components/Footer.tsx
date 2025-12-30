import { useLanguage } from "@/contexts/LanguageContext";

const Footer = () => {
  const { t } = useLanguage();

  return (
    <footer className="py-6 sm:py-8 border-t border-border">
      <div className="section-container">
        <div className="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-xs sm:text-sm text-muted-foreground text-center sm:text-left">
          <p>{t.footer.copyright}</p>
          <p>{t.footer.designedBy} <span className="text-primary">{t.footer.me}</span></p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
