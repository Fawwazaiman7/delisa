const clamp = (value, min, max) => {
  if (Number.isNaN(value)) {
    return min;
  }
  return Math.min(Math.max(value, min), max);
};

const applyConicCharts = () => {
  document.querySelectorAll('.js-conic-chart').forEach((el) => {
    const color = el.dataset.chartColor || '#B9257F';
    const fallback = Number(el.dataset.chartFallback ?? 0);
    const raw = Number.parseFloat(el.dataset.chartValue ?? fallback);
    const value = clamp(raw, 0, 100);
    el.style.background = `conic-gradient(${color} ${value}%, #F1F1F1 ${value}% 100%)`;
  });
};

const applyWidthBars = () => {
  document.querySelectorAll('.js-width-bar').forEach((el) => {
    const raw = Number.parseFloat(el.dataset.widthPercent ?? '0');
    const value = clamp(raw, 0, 100);
    el.style.width = `${value}%`;
  });
};

const applyHeightBars = () => {
  document.querySelectorAll('.js-height-bar').forEach((el) => {
    const raw = Number.parseFloat(el.dataset.heightPercent ?? '0');
    const value = clamp(raw, 0, 100);
    el.style.height = `${value}%`;
  });
};

const hydrateDashboard = () => {
  applyConicCharts();
  applyWidthBars();
  applyHeightBars();
};

document.addEventListener('DOMContentLoaded', hydrateDashboard);
window.addEventListener('pageshow', () => {
  hydrateDashboard();
});
