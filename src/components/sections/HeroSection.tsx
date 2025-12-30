import { motion } from "framer-motion";
import { Download, Mail, ChevronDown } from "lucide-react";
import { Button } from "@/components/ui/button";

const HeroSection = () => {
  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden px-4">
      {/* Background gradient */}
      <div className="absolute inset-0 bg-gradient-hero" />
      
      {/* Animated glow - smaller on mobile */}
      <div className="absolute top-1/4 left-1/2 -translate-x-1/2 w-[300px] sm:w-[400px] md:w-[600px] h-[300px] sm:h-[400px] md:h-[600px] bg-primary/5 rounded-full blur-3xl animate-glow-pulse" />
      
      <div className="section-container relative z-10 text-center pt-16 sm:pt-20">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, ease: "easeOut" }}
        >
          <span className="inline-block px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-secondary border border-border text-xs sm:text-sm text-muted-foreground mb-4 sm:mb-6">
            Junior Fullstack Developer • Laravel Specialist
          </span>
        </motion.div>

        <motion.h1
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.1, ease: "easeOut" }}
          className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight mb-4 sm:mb-6"
        >
          <span className="block sm:inline">Xây dựng hệ thống</span>{" "}
          <span className="text-gradient block sm:inline">backend vững chắc</span>
          <br className="hidden sm:block" />
          <span className="block mt-1 sm:mt-0">cho doanh nghiệp của bạn</span>
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.2, ease: "easeOut" }}
          className="text-base sm:text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto mb-8 sm:mb-10 px-2"
        >
          Junior Developer với nền tảng PHP Laravel, chuyên thiết kế kiến trúc database 
          và xây dựng API cho các hệ thống quản lý phức tạp.
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.3, ease: "easeOut" }}
          className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-4 sm:px-0"
        >
          <Button variant="hero" size="lg" className="w-full sm:w-auto">
            <Download className="w-4 h-4 sm:w-5 sm:h-5" />
            Tải CV
          </Button>
          <Button variant="heroOutline" size="lg" className="w-full sm:w-auto">
            <Mail className="w-4 h-4 sm:w-5 sm:h-5" />
            Liên hệ
          </Button>
        </motion.div>
      </div>

      {/* Scroll indicator */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ delay: 1.5 }}
        className="absolute bottom-6 sm:bottom-8 left-1/2 -translate-x-1/2"
      >
        <a href="#about" className="flex flex-col items-center text-muted-foreground hover:text-primary transition-colors">
          <span className="text-xs sm:text-sm mb-1 sm:mb-2">Khám phá thêm</span>
          <ChevronDown className="w-4 h-4 sm:w-5 sm:h-5 animate-bounce" />
        </a>
      </motion.div>
    </section>
  );
};

export default HeroSection;
