import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import LazySection from "@/components/LazySection";
import ScrollToTop from "@/components/ScrollToTop";
import HeroSection from "@/components/sections/HeroSection";
import AboutSection from "@/components/sections/AboutSection";
import TechStackSection from "@/components/sections/TechStackSection";
import ProjectsSection from "@/components/sections/ProjectsSection";
import PhilosophySection from "@/components/sections/PhilosophySection";
import CTASection from "@/components/sections/CTASection";

const Index = () => {
  return (
    <div className="min-h-screen bg-background">
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
