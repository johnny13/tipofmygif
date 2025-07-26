
# Gif Model Usage Examples

## Creating a GIF with User Association

```php
// When a user saves a GIF from the Giphy API
$giphyData = [
    'id' => '3og0IMJcSI8p6hYQXS',
    'title' => 'Educate Yourself Shooting Star GIF',
    // ... other Giphy data
];

// Create GIF and associate with current user
$gif = Gif::createFromGiphyData($giphyData, auth()->id());

// Or create without user (for public browsing)
$gif = Gif::createFromGiphyData($giphyData);
```

## User Relationships

```php
// Get all GIFs saved by a specific user
$user = User::find(1);
$userGifs = $user->gifs;

// Or use the scope
$userGifs = Gif::byUser(1)->get();

// Get the user who saved a GIF
$gif = Gif::find(1);
$savedBy = $gif->user;
```

## Querying GIFs

```php
// Get all saved GIFs (have a user)
$savedGifs = Gif::saved()->get();

// Get all GIFs saved by current user
$myGifs = Gif::byUser(auth()->id())->get();

// Get GIFs by rating
$gRatedGifs = Gif::byRating('g')->get();

// Get GIFs with verified authors
$verifiedAuthorGifs = Gif::withVerifiedAuthor()->get();

// Combine scopes
$myVerifiedGifs = Gif::byUser(auth()->id())
    ->withVerifiedAuthor()
    ->get();
```

## Controller Example

```php
class GifController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'giphy_id' => 'required|string',
            'giphy_data' => 'required|array'
        ]);

        // Check if user already saved this GIF
        $existingGif = Gif::byGiphyId($request->giphy_id)
            ->byUser(auth()->id())
            ->first();

        if ($existingGif) {
            return response()->json([
                'message' => 'GIF already saved',
                'gif' => $existingGif
            ], 409);
        }

        // Save the GIF
        $gif = Gif::createFromGiphyData(
            $request->giphy_data, 
            auth()->id()
        );

        return response()->json([
            'message' => 'GIF saved successfully',
            'gif' => $gif
        ], 201);
    }

    public function myGifs()
    {
        $gifs = Gif::byUser(auth()->id())
            ->with(['ratings', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($gifs);
    }

    public function delete($id)
    {
        $gif = Gif::byUser(auth()->id())
            ->findOrFail($id);

        $gif->delete();

        return response()->json([
            'message' => 'GIF removed from your collection'
        ]);
    }
}
```

## Database Structure

The `gifs` table now includes:

- `created_by` - Foreign key to `users.id` (nullable)
- Foreign key constraint with cascade delete
- Index for performance on user queries

## Key Features

✅ **User Association**: Track which user saved each GIF
✅ **Flexible Creation**: Can create GIFs with or without user association
✅ **Efficient Queries**: Scopes for filtering by user, rating, etc.
✅ **Relationships**: Easy access to user data and related models
✅ **Cascade Delete**: When a user is deleted, their saved GIFs are removed
✅ **OpenAPI Documentation**: Updated schema includes `created_by` field 