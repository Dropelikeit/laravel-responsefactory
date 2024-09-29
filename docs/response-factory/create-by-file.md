* [Back](../../README.md)
* [ResponseFactory](../response-factory.md)

# `ResponseFactory::createByFile`

This package provides two DTOs to insert the contents of the file into the response factory.

### StringInput

If you have the content of the file as a string, you can use StringInput.

### StreamInput

If you have a stream, you can insert it into the `StreamInput`.

### Input interface

If you can't use the string or stream input, you can create your own Input by create a class and implement the `Input` interface.
Note, however, that you must also implement the `InputToStringTransformerFactory` yourself in order to make your 
Transformer available to the ResponseFactory for your input class.

---
The `MimetypeFromFileInformationDetector` uses the PHP `FileInfo` class in its core to recognize the mime type of a given string.

The `MimetypeFromFileInformationDetector` is used by the MimeTypeDector class.

The MimeTypeDetector is injected into the ResponseFactory to determine the mimetype of the given input.
To detect the mime type, an `InputToStringTransformerFactory` is used to create the correct transformer, which takes the value
(the file content) of the input class. Next, the MimeTypeDetector
will use the content to pass it to the `MimeTypeFromFileInformationDetector` to resolve the content
to resolve the mimetype based on the content (currently with PHP's FileInfo class).

*Note*: You can replace all concrete classes with the implementation of the correct interfaces.