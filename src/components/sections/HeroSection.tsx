import { useRef, useEffect, useState } from "react";
import { motion, useScroll, useTransform } from "framer-motion";
import { Download, Mail, ChevronDown } from "lucide-react";
import { Button } from "@/components/ui/button";
import ParticleBackground from "@/components/ParticleBackground";
import { useLanguage } from "@/contexts/LanguageContext";

const HeroSection = () => {
  const { t, language } = useLanguage();
  const [displayedText, setDisplayedText] = useState({ h1: "", h2: "", h3: "" });
  const [currentLine, setCurrentLine] = useState<0 | 1 | 2 | 3>(1);
  const [typingDone, setTypingDone] = useState(false);

  const ref = useRef<HTMLElement>(null);
  const { scrollYProgress } = useScroll({
    target: ref,
    offset: ["start start", "end start"]
  });

  const backgroundY = useTransform(scrollYProgress, [0, 1], ["0%", "30%"]);
  const textY = useTransform(scrollYProgress, [0, 1], ["0%", "50%"]);
  const opacity = useTransform(scrollYProgress, [0, 0.5], [1, 0]);

  // Typing effect
  useEffect(() => {
    setDisplayedText({ h1: "", h2: "", h3: "" });
    setCurrentLine(1);
    setTypingDone(false);

    const texts = [t.hero.headline1, t.hero.headline2, t.hero.headline3];
    let lineIndex = 0;
    let charIndex = 0;
    const speed = 60;

    const typeNextChar = () => {
      if (lineIndex >= 3) {
        setTypingDone(true);
        setCurrentLine(0);
        return;
      }

      const currentText = texts[lineIndex];
      if (charIndex < currentText.length) {
        setDisplayedText(prev => {
          const key = `h${lineIndex + 1}` as "h1" | "h2" | "h3";
          return { ...prev, [key]: currentText.slice(0, charIndex + 1) };
        });
        charIndex++;
        setTimeout(typeNextChar, speed);
      } else {
        lineIndex++;
        charIndex = 0;
        setCurrentLine((lineIndex + 1) as 1 | 2 | 3);
        setTimeout(typeNextChar, 300);
      }
    };

    const startDelay = setTimeout(typeNextChar, 300);
    return () => clearTimeout(startDelay);
  }, [language, t.hero.headline1, t.hero.headline2, t.hero.headline3]);

  const Cursor = () => (
    <span className="animate-pulse">|</span>
  );

  return (
    <section ref={ref} className="relative min-h-screen flex items-center justify-center overflow-hidden px-4">
      {/* Particle background */}
      <ParticleBackground />
      
      {/* Background gradient with parallax */}
      <motion.div 
        className="absolute inset-0 bg-gradient-hero" 
        style={{ y: backgroundY }}
      />
      
      {/* Animated glow - smaller on mobile */}
      <motion.div 
        className="absolute top-1/4 left-1/2 -translate-x-1/2 w-[300px] sm:w-[400px] md:w-[600px] h-[300px] sm:h-[400px] md:h-[600px] bg-primary/5 rounded-full blur-3xl animate-glow-pulse" 
        style={{ y: backgroundY }}
      />
      
      <motion.div className="section-container relative z-10 text-center" style={{ y: textY, opacity }}>
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, ease: "easeOut" }}
        >
          <span className="inline-block px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-secondary border border-border text-xs sm:text-sm text-muted-foreground mb-4 sm:mb-6">
            {t.hero.badge}
          </span>
        </motion.div>

        <motion.h1
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight mb-4 sm:mb-6 min-h-[4.5em] sm:min-h-[3.5em]"
        >
          {displayedText.h1}
          {currentLine === 1 && <Cursor />}
          {displayedText.h2 && (
            <>
              {" "}
              <span className="text-gradient">
                {displayedText.h2}
                {currentLine === 2 && <Cursor />}
              </span>
            </>
          )}
          {displayedText.h3 && (
            <>
              <br />
              {displayedText.h3}
              {currentLine === 3 && <Cursor />}
            </>
          )}
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 4, ease: "easeOut" }}
          className="text-base sm:text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto mb-8 sm:mb-10 px-2"
        >
          {t.hero.description}
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 4.2, ease: "easeOut" }}
          className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4 sm:px-0"
        >
          <Button variant="hero" size="lg" className="w-full sm:w-auto">
            <Download className="w-4 h-4 sm:w-5 sm:h-5" />
            {t.hero.downloadCV}
          </Button>
          <a href="#contact">
            <Button variant="heroOutline" size="lg" className="w-full sm:w-auto">
              <Mail className="w-4 h-4 sm:w-5 sm:h-5" />
              {t.hero.contact}
            </Button>
          </a>
        </motion.div>
      </motion.div>

      {/* Scroll indicator */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ delay: 4.5 }}
        className="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2"
      >
        <a href="#about" className="flex flex-col items-center text-muted-foreground hover:text-primary transition-colors">
          <span className="text-xs sm:text-sm mb-1 sm:mb-2">{t.hero.scrollMore}</span>
          <ChevronDown className="w-4 h-4 sm:w-5 sm:h-5 animate-bounce" />
        </a>
      </motion.div>
    </section>
  );
};

export default HeroSection;
