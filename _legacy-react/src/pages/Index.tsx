import { Helmet } from "react-helmet-async";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import LazySection from "@/components/LazySection";
import ScrollToTop from "@/components/ScrollToTop";
import ScrollProgress from "@/components/ScrollProgress";
import HeroSection from "@/components/sections/HeroSection";
import AboutSection from "@/components/sections/AboutSection";
import TechStackSection from "@/components/sections/TechStackSection";
import ProjectsSection from "@/components/sections/ProjectsSection";
import PhilosophySection from "@/components/sections/PhilosophySection";
import CTASection from "@/components/sections/CTASection";
import LanguageTransition from "@/components/LanguageTransition";
import { useLanguage } from "@/contexts/LanguageContext";

const Index = () => {
  const { language } = useLanguage();

  return (
    <div className="min-h-screen bg-background">
      <Helmet>
        <html lang={language} />
        <title>Nguyễn Đức Độ | Web Developer Laravel – Portfolio</title>
        <meta name="description" content="Portfolio của Nguyễn Đức Độ – Backend Developer chuyên PHP Laravel, RESTful API, GraphQL. Kinh nghiệm outsource Nhật Bản & dự án thương mại điện tử." />
        <link rel="canonical" href="https://pixelpolish-pro.lovable.app/portfolio" />
      </Helmet>
      <ScrollProgress />
      <Navbar />
      <main>
        <HeroSection />
        <LazySection>
          <AboutSection />
        </LazySection>
        <LazySection>
          <TechStackSection />
        </LazySection>
        <LazySection>
          <ProjectsSection />
        </LazySection>
        <LazySection>
          <PhilosophySection />
        </LazySection>
        <LazySection>
          <CTASection />
        </LazySection>
      </main>
      <Footer />
      <ScrollToTop />
    </div>
  );
};

export default Index;
