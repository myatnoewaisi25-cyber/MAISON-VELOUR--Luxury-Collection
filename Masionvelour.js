// Cursor movement
const cursor = document.querySelector('.cursor');
const cursorRing = document.querySelector('.cursor-ring');

document.addEventListener('mousemove', (e) => {
  cursor.style.left = e.clientX + 'px';
  cursor.style.top = e.clientY + 'px';

  cursorRing.style.left = e.clientX + 'px';
  cursorRing.style.top = e.clientY + 'px';
});