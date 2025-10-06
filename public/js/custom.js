const consonantMap = {
  "kh":"ख","k":"क","g":"ग","gh":"घ","ng":"ङ",
  "ch":"च","chh":"छ","j":"ज","jh":"झ","ny":"ञ",
  "t":"त","th":"थ","d":"द","dh":"ध","n":"न","nn":"ण",
  "p":"प","ph":"फ","b":"ब","bh":"भ",
  "m":"म","y":"य","r":"र","l":"ल","v":"व","w":"व",
  "sh":"श","s":"स","h":"ह","z":"ज़","f":"फ़","q":"क"
};

const vowelIndependentDefault = {
  "a":"आ","aa":"आ","i":"इ","ii":"ई","ee":"ई","u":"उ","uu":"ऊ",
  "e":"ए","ai":"ऐ","o":"ओ","au":"औ"
};

const matraDefault = {
  "a":"ा","aa":"ा",
  "i":"ि","ii":"ी","ee":"ी","u":"ु","uu":"ू",
  "e":"े","ai":"ै","o":"ो","au":"ौ"
};

const VIRAMA = "\u094D";

function buildTokenList() {
  const vowelIndependent = Object.assign({}, vowelIndependentDefault);
  const matra = Object.assign({}, matraDefault);
  const vowelKeys = Object.keys(vowelIndependent);
  const consonantKeys = Object.keys(consonantMap);
  const keys = Array.from(new Set([...vowelKeys, ...consonantKeys]));
  keys.sort((a,b) => b.length - a.length);
  return { keys, vowelIndependent, matra };
}

function tokenize(input, keys) {
  const tokens = [];
  let i = 0;
  const s = input.toLowerCase();
  while (i < s.length) {
    let matched = false;
    const ch = s[i];
    if (/\s/.test(ch)) { tokens.push({type:"space",text:ch}); i++; continue; }
    if (/[^a-z0-9]/.test(ch)) { tokens.push({type:"punct",text:ch}); i++; continue; }
    for (const k of keys) {
      if (s.startsWith(k, i)) {
        tokens.push({type:"latin",text:k});
        i += k.length;
        matched = true;
        break;
      }
    }
    if (!matched) { tokens.push({type:"latin",text:s[i]}); i++; }
  }
  return tokens;
}

function transliterateLatin(text) {
  const {keys, vowelIndependent, matra} = buildTokenList();
  const tokens = tokenize(text, keys);

  let out = "";
  let lastWasConsonant = false;

  for (let i=0;i<tokens.length;i++) {
    const t = tokens[i];
    if (t.type==="space"||t.type==="punct") { out+=t.text; lastWasConsonant=false; continue; }
    const tk = t.text;

    if (vowelIndependent.hasOwnProperty(tk)) {
      if (lastWasConsonant) {
        out += matra[tk] ?? "";
        lastWasConsonant = false;
      } else {
        out += vowelIndependent[tk];
        lastWasConsonant = false;
      }
      continue;
    }

    if (consonantMap.hasOwnProperty(tk)) {
      if (lastWasConsonant) out += VIRAMA;
      out += consonantMap[tk];
      lastWasConsonant = true;
      continue;
    }

    out += tk;
    lastWasConsonant=false;
  }
  return out;
}

function attachMarathiTransliteration(input) {
  if (!input || input.tagName !== 'INPUT' && input.tagName !== 'TEXTAREA') return;

  input._raw = "";
  if (input.dataset && input.dataset.raw) {
    input._raw = input.dataset.raw.toString();
  } else if (/[a-zA-Z]/.test(input.value)) {
    input._raw = input.value.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();
  } else {
    input._raw = "";
  }

  if (input._raw.length) input.value = transliterateLatin(input._raw);

  let composing = false;
  input.addEventListener('compositionstart', () => composing = true);
  input.addEventListener('compositionend', () => { composing = false; });

  input.addEventListener('keydown', function(e) {
    if (composing) return; // let IME work
    if (e.ctrlKey || e.metaKey || e.altKey) return;

    if (e.key.length === 1 && /[a-zA-Z0-9]/.test(e.key)) {
      e.preventDefault();
      input._raw += e.key.toLowerCase();
      input.value = transliterateLatin(input._raw);
      return;
    }

    if (e.key === 'Backspace') {
      e.preventDefault();
      input._raw = input._raw.slice(0, -1);
      input.value = transliterateLatin(input._raw);
      return;
    }

    if (e.key === ' ' || e.key === 'Enter') {
      e.preventDefault();
      input._raw += ' ';
      input.value = transliterateLatin(input._raw);
      return;
    }

  });

  input.addEventListener('paste', function(e) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text') || '';
    input._raw += pasted.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();
    input.value = transliterateLatin(input._raw);
  });

  input.addEventListener('blur', function() {
    if (input.dataset) input.dataset.raw = input._raw;
  });
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.translate-to-mr').forEach(el => attachMarathiTransliteration(el));
});
