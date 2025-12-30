import { useState, useRef, ReactNode, MouseEvent } from "react";
import { motion } from "framer-motion";

interface Card3DProps {
  children: ReactNode;
  className?: string;
}

const Card3D = ({ children, className = "" }: Card3DProps) => {
  const [rotateX, setRotateX] = useState(0);
  const [rotateY, setRotateY] = useState(0);
  const [scale, setScale] = useState(1);
  const [glowPosition, setGlowPosition] = useState({ x: 50, y: 50 });
  const [isHovered, setIsHovered] = useState(false);
  const cardRef = useRef<HTMLDivElement>(null);

  const handleMouseMove = (e: MouseEvent<HTMLDivElement>) => {
    if (!cardRef.current) return;
    
    const card = cardRef.current;
    const rect = card.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    
    const mouseX = e.clientX - centerX;
    const mouseY = e.clientY - centerY;
    
    const rotateXValue = (mouseY / (rect.height / 2)) * -6;
    const rotateYValue = (mouseX / (rect.width / 2)) * 6;
    
    // Calculate glow position as percentage
    const glowX = ((e.clientX - rect.left) / rect.width) * 100;
    const glowY = ((e.clientY - rect.top) / rect.height) * 100;
    
    setRotateX(rotateXValue);
    setRotateY(rotateYValue);
    setGlowPosition({ x: glowX, y: glowY });
  };

  const handleMouseEnter = () => {
    setScale(1.01);
    setIsHovered(true);
  };

  const handleMouseLeave = () => {
    setRotateX(0);
    setRotateY(0);
    setScale(1);
    setIsHovered(false);
    setGlowPosition({ x: 50, y: 50 });
  };

  return (
    <motion.div
      ref={cardRef}
      onMouseMove={handleMouseMove}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      style={{
        transformStyle: "preserve-3d",
        perspective: "1000px",
      }}
      animate={{
        rotateX,
        rotateY,
        scale,
      }}
      transition={{
        type: "spring",
        stiffness: 400,
        damping: 25,
      }}
      className={`relative ${className}`}
    >
      {/* Gradient border glow effect */}
      <motion.div
        className="absolute -inset-[1px] rounded-xl sm:rounded-2xl opacity-0 pointer-events-none"
        style={{
          background: `radial-gradient(600px circle at ${glowPosition.x}% ${glowPosition.y}%, hsl(var(--primary) / 0.4), transparent 40%)`,
        }}
        animate={{
          opacity: isHovered ? 1 : 0,
        }}
        transition={{ duration: 0.3 }}
      />
      
      {/* Inner glow effect */}
      <motion.div
        className="absolute inset-0 rounded-xl sm:rounded-2xl opacity-0 pointer-events-none overflow-hidden"
        animate={{
          opacity: isHovered ? 1 : 0,
        }}
        transition={{ duration: 0.3 }}
      >
        <div 
          className="absolute inset-0"
          style={{
            background: `radial-gradient(400px circle at ${glowPosition.x}% ${glowPosition.y}%, hsl(var(--primary) / 0.08), transparent 50%)`,
          }}
        />
      </motion.div>

      <div
        style={{
          transformStyle: "preserve-3d",
        }}
        className="relative z-10"
      >
        {children}
      </div>
    </motion.div>
  );
};

export default Card3D;
