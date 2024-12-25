import dayjs from 'dayjs';

export function formateDatetime(val, fmt = null, locale = 'id-ID') {
  let date;
  if (val instanceof Date) {
    console.log('accepting date')
    date = val;
  }
  else if (typeof (val) === 'string') {
    console.log('accepting string')
    date = new Date(val);
  }
  else {
    throw new Error('val must be string or Date object');
  }

  return dayjs(this.currentDate).format(fmt);
}
