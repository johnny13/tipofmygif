// // src/api/giphy.ts
// import { api } from 'boot/axios';
// import type { AxiosResponse } from 'axios';
// import { AxiosError } from 'axios';

// interface GiphyData {
//   title?: string;
//   url: string;
//   width?: number;
//   height?: number;
//   giphy_id: string;
//   [key: string]: any;
// }

// interface SaveGifResponse {
//   status: string;
//   data: {
//     id: string;
//     giphy_id: string;
//     giphy_data: GiphyData;
//   };
// }

// export async function saveGif(giphy_id: string, giphy_data: GiphyData): Promise<SaveGifResponse> {
//   try {
//     const response: AxiosResponse<SaveGifResponse> = await api.post('/api/gifs/save', giphy_data, {
//       params: { giphy_id },
//     });
//     return response.data;
//   } catch (error) {
//     if (error instanceof AxiosError) {
//       throw new Error(
//         error.response?.data?.message || 'Failed to save GIF. Please try again.',
//       );
//     }
//     throw new Error('An unexpected error occurred while saving the GIF.');
//   }
// }
