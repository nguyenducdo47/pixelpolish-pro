import { useState } from "react";
import { Download } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { useLanguage } from "@/contexts/LanguageContext";

interface CVDownloadDialogProps {
  variant?: "hero" | "heroOutline" | "outline" | "default";
  size?: "lg" | "sm" | "default";
  className?: string;
}

const CVDownloadDialog = ({ variant = "hero", size = "lg", className }: CVDownloadDialogProps) => {
  const { t, language } = useLanguage();
  const [open, setOpen] = useState(false);

  const handleDownload = (lang: "vi" | "en") => {
    const url = lang === "vi" ? "/cv/Duc-Do-Nguyen_Web-Developer(Vi).pdf" : "/cv/Duc-Do-Nguyen_Web-Developer(En).pdf";
    const link = document.createElement("a");
    link.href = url;
    link.download = lang === "vi" ? "Duc-Do-Nguyen_Web-Developer(Vi).pdf" : "Duc-Do-Nguyen_Web-Developer(En).pdf";
    link.click();
    setOpen(false);
  };

  const labels = language === "vi"
    ? { title: "Chọn ngôn ngữ CV", vi: "Tiếng Việt", en: "Tiếng Anh" }
    : { title: "Choose CV Language", vi: "Vietnamese", en: "English" };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button variant={variant} size={size} className={className}>
          <Download className="w-4 h-4 sm:w-5 sm:h-5" />
          {language === "vi" ? t.hero.downloadCV : t.hero.downloadCV}
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>{labels.title}</DialogTitle>
        </DialogHeader>
        <div className="flex flex-col gap-3 mt-2">
          <Button
            variant="outline"
            size="lg"
            className="w-full justify-start gap-3 h-14"
            onClick={() => handleDownload("vi")}
          >
            <span className="text-xl">🇻🇳</span>
            <span className="font-medium">{labels.vi}</span>
          </Button>
          <Button
            variant="outline"
            size="lg"
            className="w-full justify-start gap-3 h-14"
            onClick={() => handleDownload("en")}
          >
            <span className="text-xl">🇺🇸</span>
            <span className="font-medium">{labels.en}</span>
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
};

export default CVDownloadDialog;