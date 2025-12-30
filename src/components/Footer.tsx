const Footer = () => {
  return (
    <footer className="py-8 border-t border-border">
      <div className="section-container">
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-muted-foreground">
          <p>© 2024 DevPortfolio. Built with Laravel passion 💙</p>
          <p>Designed & Developed by <span className="text-primary">Me</span></p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
