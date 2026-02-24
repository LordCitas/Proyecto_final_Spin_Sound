import React from 'react';

export default function AddToCart({ endpoint, viniloId, onAdded }) {
  const handleClick = async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('vinilo_id', viniloId);
    // CSRF token must be present in DOM as meta tag or input; we'll try to read from a meta
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
      formData.append('_token', tokenMeta.getAttribute('content'));
    } else {
      // Fallback: look for a hidden input with name _token inside the closest form
      const btn = e.target;
      const form = btn.closest('form');
      if (form) {
        const input = form.querySelector('input[name="_token"]');
        if (input) formData.append('_token', input.value);
      }
    }

    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json'
        }
      });

      const data = await res.json();
      if (data && data.ok) {
        if (typeof onAdded === 'function') onAdded(data);
      } else {
        console.error('Error add to cart', data);
      }
    } catch (err) {
      console.error(err);
    }
  };

  return (
    <button onClick={handleClick} className="w-full rounded-lg py-2 text-sm font-bold text-white transition-transform hover:scale-105" style={{backgroundColor: '#e00000'}}>
      Añadir al carrito
    </button>
  );
}

