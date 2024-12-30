import { usePage } from "@inertiajs/vue3";

export function score_to_letter(score) {
  if (score > 90) return "A";
  if (score > 80) return "B";
  if (score > 70) return "C";
  if (score > 60) return "D";
  return "E";
}

export function score_to_color(score) {
  if (score > 90) return "blue";
  if (score > 80) return "green";
  if (score > 70) return "orange";
  if (score > 60) return "red";
  return "red";
}

export function create_gender_options() {
  return [
    { value: null, label: 'Tidak ditentukan' },
    { value: 'male', label: 'Laki-laki' },
    { value: 'female', label: 'Perempuan' },
  ];
}

export function format_duration(duration) {
  const durationInt = parseInt(duration);
  const hours = Math.floor(durationInt / 3600);
  const minutes = Math.floor((durationInt % 3600) / 60);
  const seconds = durationInt % 60;
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}
/**
 * Memeriksa apakah current user role ada di roles
 * @param {string | Array} roles
 * @returns boolean
 */
export function check_role(roles) {
  const page = usePage();
  if (!Array.isArray(roles))
    roles = [roles];
  return roles.includes(page.props.auth.user.role);
}

export function format_gender(gender) {
  if (gender === 'male') return 'Laki-laki';
  if (gender === 'female') return 'Perempuan';
  return 'Tidak ditentukan';
}

export function create_options(data) {
  return Object.entries(data)
    .map(([key, value]) => ({ 'value': key, 'label': value }));
}

export function create_options_v2(items, valueProp, labelProp) {
  return items.map((item) => {
    return { 'value': item[valueProp], 'label': item[labelProp] };
  });
}


export function create_options_from_users(items) {
  return items.map((user) => {
    return { 'value': user.id, 'label': `${user.username} - ${user.name}` };
  });
}

export function create_options_from_clients(items) {
  return items.map((item) => {
    return { 'value': item.id, 'label': `${item.name}` };
  });
}

export async function scrollToFirstErrorField(ref) {
  const element = ref.getNativeElement();
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    element.focus();
  }
}
