import React from 'react';

type FlagProps = {
  localeCode: string;
}

export const Flag = ({localeCode}: FlagProps) => {
  const region = localeCode.split('_')[localeCode.split('_').length - 1];

  return (
    <i className={`flag flag-${region.toLowerCase()}`}/>
  );
};
